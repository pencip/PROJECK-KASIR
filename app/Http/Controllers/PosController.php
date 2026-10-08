<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Promotion;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $categories = Menu::where('is_active', true)->distinct()->pluck('category');
        $menus      = Menu::where('is_active', true)->orderBy('name')->get();

        // Ambil promosi yang sudah disetujui manajer dan masih dalam periode aktif
        $activePromotions = Promotion::where('status', 'approved')
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->with('creator')
            ->orderBy('tanggal_selesai')
            ->get();

        // Ambil juga semua promosi approved (termasuk yang belum/sudah expired) untuk referensi lengkap
        $allApprovedPromotions = Promotion::where('status', 'approved')
            ->with('creator', 'verifier')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('pos', compact('menus', 'categories', 'activePromotions', 'allApprovedPromotions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'paid_amount'    => 'required|numeric|min:0',
            'order_type'     => 'nullable|in:dine_in,takeaway,online',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:menus,id',
            'items.*.qty'    => 'required|integer|min:1',
        ]);

        try {
            $transaction = DB::transaction(function () use ($request) {
                $totalCalculated = 0;
                $detailsToInsert = [];

                foreach ($request->items as $itemData) {
                    $menu = Menu::where('id', $itemData['id'])->lockForUpdate()->firstOrFail();

                    if ($menu->stock < $itemData['qty']) {
                        throw new \Exception("Stok untuk {$menu->name} tidak mencukupi (Tersisa: {$menu->stock}).");
                    }

                    $subtotal = $menu->price * $itemData['qty'];
                    $totalCalculated += $subtotal;

                    // Kurangi stok menu
                    $menu->decrement('stock', $itemData['qty']);

                    $detailsToInsert[] = [
                        'menu_id'    => $menu->id,
                        'menu_name'  => $menu->name,
                        'price'      => $menu->price,
                        'quantity'   => $itemData['qty'],
                        'subtotal'   => $subtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($request->payment_method === 'cash' && $request->paid_amount < $totalCalculated) {
                    throw new \Exception('Nominal uang tunai yang dibayarkan kurang.');
                }

                $paidAmount   = $request->payment_method === 'cash' ? $request->paid_amount : $totalCalculated;
                $changeAmount = $paidAmount - $totalCalculated;

                $trx = Transaction::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'user_id'        => auth()->id(),
                    'total_amount'   => $totalCalculated,
                    'payment_method' => $request->payment_method,
                    'order_type'     => $request->input('order_type', 'dine_in'),
                    'paid_amount'    => $paidAmount,
                    'change_amount'  => $changeAmount,
                    'status'         => 'completed',
                ]);

                foreach ($detailsToInsert as &$detail) {
                    $detail['transaction_id'] = $trx->id;
                }
                TransactionDetail::insert($detailsToInsert);

                return $trx->load('details', 'cashier');
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'data'    => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
