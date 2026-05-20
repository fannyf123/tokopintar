<?php

namespace App\Http\Controllers;

use App\Http\Requests\PelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(): View
    {
        $items = Pelanggan::orderBy('nama')->paginate(15);
        return view('pelanggan.index', compact('items'));
    }

    public function create(): View
    {
        return view('pelanggan.form', ['pelanggan' => new Pelanggan()]);
    }

    public function store(PelangganRequest $request): RedirectResponse
    {
        Pelanggan::create($request->validated());
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan dibuat.');
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('pelanggan.form', compact('pelanggan'));
    }

    public function update(PelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update($request->validated());
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        if ($pelanggan->penjualans()->exists()) {
            return back()->with('error', 'Pelanggan punya transaksi, tidak bisa dihapus.');
        }
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan dihapus.');
    }
}
