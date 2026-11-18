<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriItemsController extends Controller
{
    public function index()
    {
        return view('kategori_items.index.index');
    }

    public function search(Request $request)
    {
        $query = KategoriItem::query();
        
        if ($request->filled('kode')) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        $data = $query->select('id', 'kode', 'nama')
                      ->orderBy('id')
                      ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function formView($method, $id = 0)
    {
        $item = ($method == 'new') ? [] : KategoriItem::findOrFail($id);
        
        return view('kategori_items.form.index', [
            'item' => $item,
            'method' => $method,
        ]);
    }

    public function singleView($kode)
    {
        $data = KategoriItem::with(['masterItems' => function ($query) {
            $query->orderBy('id');
        }])->where('kode', $kode)->firstOrFail();

        return view('kategori_items.single.index', [
            'data' => $data,
        ]);
    }

    public function exportPdf($kode)
    {
        $data = KategoriItem::with(['masterItems' => function ($query) {
            $query->orderBy('id');
        }])->where('kode', $kode)->firstOrFail();

        $printedAt = now()->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB';

        $pdf = Pdf::loadView('kategori_items.single.pdf', [
            'data' => $data,
            'printedAt' => $printedAt,
        ])->setPaper('a4', 'portrait');

        $safeKode = preg_replace('/[^A-Za-z0-9\-_]/', '_', $data->kode);

        return $pdf->download('kategori-' . $safeKode . '.pdf');
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
       $kategori = ($method == 'new') ? new KategoriItem() : KategoriItem::findOrFail($id);

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kategori_items')->ignore($kategori->id)
                ->whereNull('deleted_at'),
            ],
            'nama' => 'required|string|max:255',
        ]);

        $kategori->kode = $validated['kode'];
        $kategori->nama = $validated['nama'];
        $kategori->save();

        return redirect ('kategori-items');
    }

    public function delete($id)
    {
        $kategori = KategoriItem::findOrFail($id);
        $kategori->masterItems()->detach();
        $kategori->delete();

        return redirect('kategori-items');
    }
}
