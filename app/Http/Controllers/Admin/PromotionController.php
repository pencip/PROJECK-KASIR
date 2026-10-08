<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('creator', 'verifier')->latest()->paginate(10);
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_promo'      => 'required|string|max:255',
            'gambar'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'nilai_diskon'    => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('promotions', 'public');
        }

        $validated['admin_id'] = auth()->id();
        $validated['status']   = 'pending';

        Promotion::create($validated);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promosi berhasil diajukan dan menunggu verifikasi manajer.');
    }

    public function edit(Promotion $promotion)
    {
        // Admin hanya bisa edit promosi miliknya sendiri
        if ($promotion->admin_id !== auth()->id()) {
            return redirect()->route('admin.promotions.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit promosi ini.');
        }

        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        if ($promotion->admin_id !== auth()->id()) {
            return redirect()->route('admin.promotions.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah promosi ini.');
        }

        $validated = $request->validate([
            'nama_promo'      => 'required|string|max:255',
            'gambar'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tipe_diskon'     => 'required|in:persen,nominal',
            'nilai_diskon'    => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // Upload gambar baru jika ada, hapus gambar lama
        if ($request->hasFile('gambar')) {
            if ($promotion->gambar) {
                Storage::disk('public')->delete($promotion->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('promotions', 'public');
        }

        // Jika promosi sudah approved/rejected dan admin mengedit, reset ke pending untuk review ulang
        if (in_array($promotion->status, ['approved', 'rejected'])) {
            $validated['status']            = 'pending';
            $validated['alasan_verifikasi'] = null;
            $validated['verified_by']       = null;
        }

        $promotion->update($validated);

        $pesan = in_array($promotion->getOriginal('status'), ['approved', 'rejected'])
            ? 'Promosi berhasil diperbarui dan diajukan ulang untuk verifikasi manajer.'
            : 'Promosi berhasil diperbarui.';

        return redirect()->route('admin.promotions.index')->with('success', $pesan);
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->admin_id !== auth()->id()) {
            return redirect()->route('admin.promotions.index')
                ->with('error', 'Anda tidak bisa menghapus promosi ini.');
        }

        // Hapus file gambar dari storage
        if ($promotion->gambar) {
            Storage::disk('public')->delete($promotion->gambar);
        }

        $promotion->delete();

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promosi berhasil dihapus.');
    }
}