<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $items = Supplier::orderBy('nama')->paginate(15);
        return view('supplier.index', compact('items'));
    }

    public function create(): View
    {
        return view('supplier.form', ['supplier' => new Supplier()]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());
        return redirect()->route('supplier.index')->with('success', 'Supplier dibuat.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('supplier.form', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());
        return redirect()->route('supplier.index')->with('success', 'Supplier diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->barangs()->exists() || $supplier->pembelians()->exists()) {
            return back()->with('error', 'Supplier sudah dipakai, tidak bisa dihapus.');
        }
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier dihapus.');
    }
}
