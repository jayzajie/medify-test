<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Kategori - {{ $data->kode }}</title>
    <style>
        @page {
            margin: 30px 30px 60px 30px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }
        h2 {
            margin: 0 0 12px 0;
            font-size: 16px;
        }
        .info-table {
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 8px 3px 0;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
        }
        table.items th, table.items td {
            border: 1px solid #888;
            padding: 6px 8px;
            text-align: left;
        }
        table.items th {
            background-color: #efefef;
        }
        .footer {
            position: fixed;
            bottom: -38px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9.5px;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Dicetak pada: {{ $printedAt }}
    </div>

    <h2>Detail Kategori Item</h2>

    <table class="info-table">
        <tr>
            <td><strong>Kode Kategori</strong></td>
            <td>:</td>
            <td>{{ $data->kode }}</td>
        </tr>
        <tr>
            <td><strong>Nama Kategori</strong></td>
            <td>:</td>
            <td>{{ $data->nama }}</td>
        </tr>
    </table>

    <h4>Daftar Master Items</h4>
    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Item</th>
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
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->supplier }}</td>
                    <td>Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td>{{ $item->laba }}%</td>
                    <td>Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada item dalam kategori ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>