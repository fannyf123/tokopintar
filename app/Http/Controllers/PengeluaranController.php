<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengeluaranRequest;
use App\Models\Pengeluaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    public function index(): View
    {
        $items = Pengeluaran::with('user')->orderByDesc('tanggal')->paginate(15);
        return view('pengeluaran.index', compact('items'));
    }

    public function create(): View
    {
        return view('pengeluaran.form', [
            'pengeluaran' => new Pengeluaran(['tanggal' => now()->toDateString()]),
        ]);
    }

    public function store(PengeluaranRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }
        $data['dibuat_oleh'] = auth()->id();
        Pengeluaran::create($data);
        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran dicatat.');
    }

    public function show(Pengeluaran $pengeluaran): View
    {
        return view('pengeluaran.show', compact('pengeluaran'));
    }

    public function edit(Pengeluaran $pengeluaran): View
    {
        return view('pengeluaran.form', compact('pengeluaran'));
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }
        $pengeluaran->update($data);
        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran): RedirectResponse
    {
        $pengeluaran->delete();
        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran dihapus.');
    }
}
