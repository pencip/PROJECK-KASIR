<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionApprovalController extends Controller
{
    public function index()
    {
        $pendingPromotions = Promotion::where('status', 'pending')->latest()->get();
        $historyPromotions = Promotion::whereIn('status', ['approved', 'rejected'])->latest()->take(20)->get();
        return view('manager.promotions.index', compact('pendingPromotions', 'historyPromotions'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected',
            'alasan_verifikasi' => 'nullable|string|max:500',
        ]);

        $promotion = Promotion::findOrFail($id);
        $promotion->update([
            'status' => $request->action,
            'alasan_verifikasi' => $request->alasan_verifikasi,
            'verified_by' => auth()->id(),
        ]);

        $pesan = $request->action === 'approved' ? 'Promosi disetujui!' : 'Promosi ditolak.';
        return back()->with('success', $pesan);
    }
}