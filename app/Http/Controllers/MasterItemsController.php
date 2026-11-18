<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use App\Models\MasterItem;
use App\Support\MasterItemsExcelExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::query();

        if ($request->filled('kode')) {
            $data_search->where('kode', $kode);
        }
        if ($request->filled('nama')) {
            $data_search->where('nama', 'LIKE', '%' . $nama . '%');
        }
        if ($request->filled('hargamin')) {
            $data_search->where('harga_beli', '>=', $hargamin);
        }
        if ($request->filled('hargamax')) {
            $data_search->where('harga_beli', '<=', $hargamax);
        }

        $data_search = $data_search
            ->with('kategoriItems:id,kode,nama')
            ->select('id', 'kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'foto')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data_search,
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = MasterItem::with('kategoriItems')->findOrFail($id);
        }

        $data['item'] = $item;
        $data['method'] = $method;
        $data['kategoris'] = KategoriItem::orderBy('nama')->get();

        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::with('kategoriItems')->where('kode', $kode)->firstOrFail();

        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0',
            'supplier' => 'required|string',
            'jenis' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori_ids' => 'nullable|array',
            'kategori_ids.*' => 'exists:kategori_items,id',
        ]);

        if ($method == 'new') {
            $data_item = new MasterItem;
            $lastId = (MasterItem::withTrashed()->max('id') ?? 0) + 1;
            $kode = str_pad($lastId, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = MasterItem::findOrFail($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
            if (!empty($data_item->foto)) {
                Storage::disk('public')->delete($data_item->foto);
            }
            $data_item->foto = $request->file('foto')->store('master_items', 'public');
        }

        $data_item->save();
        $data_item->kategoriItems()->sync($request->input('kategori_ids', []));

        return redirect('master-items');
    }

    public function delete($id)
    {
        $item = MasterItem::findOrFail($id);
        if (!empty($item->foto)) {
            Storage::disk('public')->delete($item->foto);
        }
        $item->kategoriItems()->detach();
        $item->delete();

        return redirect('master-items');
    }

    public function exportExcel(MasterItemsExcelExporter $exporter)
    {
        $items = MasterItem::with('kategoriItems')->orderBy('id')->get();
        $path = $exporter->create($items);

        return response()->download(
            $path,
            'master-items-' . now()->format('Ymd-His') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach ($data as $item) {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100, 1000000);
            $item->laba = rand(10, 99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);
        return $array[$random];
    }
}