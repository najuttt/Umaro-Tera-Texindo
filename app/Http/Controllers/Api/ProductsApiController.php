<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;

class ProductsApiController extends Controller
{
    /**
     * GET /api/products
     * Support:
     * - search (?q=)
     * - kategori (?kategori=)
     * - sorting (?sort=)
     * - pagination (?page=)
     */
    public function index(Request $request)
    {
        $query = Item::with('category');

        /* ======================
         | SEARCH
         |======================*/
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        /* ======================
         | FILTER KATEGORI
         |======================*/
        if ($request->filled('kategori') && $request->kategori !== 'none') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->kategori);
            });
        }

        /* ======================
         | SORTING
         |======================*/
        match ($request->sort) {
            'stok_terbanyak' => $query->orderBy('stock', 'desc'),
            'stok_sedikit'   => $query->orderBy('stock', 'asc'),
            'terbaru'        => $query->latest('created_at'),
            'terlama'        => $query->oldest('created_at'),
            'nama_az'        => $query->orderBy('name', 'asc'),
            'nama_za'        => $query->orderBy('name', 'desc'),
            default          => $query->orderBy('stock', 'desc'),
        };

        /* ======================
         | PAGINATION
         |======================*/
        $items = $query->paginate(12);

        /* ======================
         | FORMAT RESPONSE (API FRIENDLY)
         |======================*/
        $data = $items->getCollection()->map(function ($item) {
            return [
                'id'       => $item->id,
                'code'     => $item->code,
                'name'     => $item->name,
                'price'    => $item->price,
                'stock'    => $item->stock,
                'category' => $item->category?->name,
                'image'    => $item->image
                    ? asset('storage/' . $item->image)
                    : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
            'data' => $data,
        ]);
    }

    /**
     * GET /api/products/{id}
     * Detail produk
     */
    public function show($id)
    {
        $item = Item::with('category')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id'       => $item->id,
                'code'     => $item->code,
                'name'     => $item->name,
                'price'    => $item->price,
                'stock'    => $item->stock,
                'category' => $item->category?->name,
                'image'    => $item->image
                    ? asset('storage/' . $item->image)
                    : null,
            ],
        ]);
    }
}
