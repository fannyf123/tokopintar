<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $items = Kategori::orderBy('nama')->paginate(15);
        return view('kategori.index', compact('items'));
    }

    public function create(): View
    {
        return view('kategori.form', ['kategori' => new Kategori()]);
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());
        return redirect()->route('kategori.index')->with('success', 'Kategori dibuat.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori.form', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());
        return redirect()->route('kategori.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->barangs()->exists()) {
            return back()->with('error', 'Kategori dipakai oleh barang, tidak bisa dihapus.');
        }
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori dihapus.');
    }
}
