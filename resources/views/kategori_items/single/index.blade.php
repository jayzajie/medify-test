@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{url('kategori-items')}}" class="btn btn-secondary">Kembali ke Daftar Item</a>
            </div>
            <div class="card">
                <div class="card-header">Detail Kategori Item</div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">Kode</th>
                            <td>:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                    </table>

                    <div class="mb-3">
                        <a class="btn btn-success" href="{{ url('kategori-items/pdf/' . rawurlencode($data->kode)) }}">Download PDF</a>
                        <a class="btn btn-info" href="{{url('kategori-items/form/edit')}}/{{$data->id}}">Edit</a>
                        <a class="btn btn-danger" href="{{url('kategori-items/delete')}}/{{$data->id}}" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </div>

                    <h5 class="mt-4">Daftar Item dalam Kategori Ini</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Supplier</th>
                                <th>Harga Beli</th>
                                <th>Laba (%)</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data->masterItems as $index => $item)
                                @php
                                    $hargaJual = round($item->harga_beli + ($item->harga_beli * $item->laba / 100));
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ url('master-items/view/' . rawurlencode($item->kode)) }}">{{ $item->kode }}</a></td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->jenis }}</td>
                                    <td>{{ $item->supplier }}</td>
                                    <td>{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                    <td>{{ $item->laba }}%</td>
                                    <td>{{ number_format($hargaJual, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada item dalam kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection