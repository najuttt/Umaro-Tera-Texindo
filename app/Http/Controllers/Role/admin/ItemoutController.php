<?php

namespace App\Http\Controllers\Role\admin;

use App\Http\Controllers\Controller;
use App\Models\Item_out;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ItemoutController extends Controller
{
    // =======================================================
    // 📝 INDEX
    // =======================================================
    public function index(Request $request)
    {
        $query = Item_out::with(['item', 'supplier', 'creator']);

        // ==============================
        // 📅 FILTER TANGGAL
        // ==============================
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // ==============================
        // 🔎 FILTER PENCARIAN
        // ==============================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', fn($sub) =>
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                );
            });
        }

        // ==============================
        // ⚖️ SORTING (URUTKAN BERDASARKAN QTY)
        // ==============================
        if ($request->filled('sort_qty')) {
            $query->orderBy('quantity', $request->sort_qty);
        } else {
            $query->latest();
        }

        // ==============================
        // 📄 PAGINATION
        // ==============================
        $perPage = $request->get('per_page', 10);
        $itemOuts = $query->paginate($perPage)->withQueryString();

        return view('role.super_admin.item_out.index', compact('itemOuts'));
    }

    // =======================================================
    // ➕ CREATE
    // =======================================================
    public function create()
    {
        $items = Item::all();
        $suppliers = Supplier::all();
        return view('role.super_admin.item_out.create', compact('items', 'suppliers'));
    }

    // =======================================================
    // 💾 STORE
    // =======================================================
    public function store(Request $request)
    {
        $request->validate([
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|integer|min:1',
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_keluar' => 'nullable|date',
        ]);

        $item_out = Item_out::create([
            'item_id'       => $request->item_id,
            'quantity'      => $request->quantity,
            'supplier_id'   => $request->supplier_id,
            'tanggal_keluar'=> $request->tanggal_keluar ?? null,
            'created_by'    => Auth::id(),
        ]);

        // Kurangi stok item
        $item = Item::findOrFail($request->item_id);
        $item->stock -= $request->quantity;
        $item->save();

        return redirect()->route('super_admin.item_out.index')
            ->with('success', 'Data berhasil ditambahkan & stok diperbarui');
    }

    // =======================================================
    // ✏️ EDIT
    // =======================================================
    public function edit(Item_out $item_out)
    {
        $items = Item::all();
        $suppliers = Supplier::all();
        return view('role.super_admin.item_out.edit', compact('item_out', 'items', 'suppliers'));
    }

    // =======================================================
    // 🔁 UPDATE
    // =======================================================
    public function update(Request $request, Item_out $item_out)
    {
        $request->validate([
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|integer|min:1',
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_keluar' => 'nullable|date',
        ]);

        $oldItemId = $item_out->item_id;
        $oldQty = $item_out->quantity;

        // Update stok jika item berubah
        if ($oldItemId != $request->item_id) {
            $oldItem = Item::findOrFail($oldItemId);
            $oldItem->stock += $oldQty; // kembalikan stok lama
            $oldItem->save();

            $newItem = Item::findOrFail($request->item_id);
            $newItem->stock -= $request->quantity; // kurangi stok baru
            $newItem->save();
        } else {
            $diff = $request->quantity - $oldQty;
            $item = Item::findOrFail($request->item_id);
            $item->stock -= $diff;
            $item->save();
        }

        $item_out->update([
            'item_id'       => $request->item_id,
            'quantity'      => $request->quantity,
            'supplier_id'   => $request->supplier_id,
            'tanggal_keluar'=> $request->tanggal_keluar,
        ]);

        return redirect()->route('super_admin.item_out.index')
            ->with('success', 'Data berhasil diupdate & stok diperbarui');
    }

    // =======================================================
    // ❌ DESTROY
    // =======================================================
    public function destroy(Item_out $item_out)
    {
        $item = Item::findOrFail($item_out->item_id);
        $item->stock += $item_out->quantity; // kembalikan stok
        $item->save();

        $item_out->delete();

        return redirect()->route('super_admin.item_out.index')
            ->with('success', 'Data berhasil dihapus & stok diperbarui');
    }
}
