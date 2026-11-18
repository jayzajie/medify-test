<form method="POST" enctype="multipart/form-data">
    @csrf
    @if($method == 'edit')
    <div class="form-group">
        <label>Kode Barang</label>
        <input type="text" class="form-control" name="kode_barang" required readonly value="{{$item->kode ?? ''}}">
    </div>
    @endif

    <div class="form-group mt-2">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required value="{{ old('nama', $item->nama ?? '') }}">
        @error('nama')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mt-2">
        <label>Harga Beli</label>
        <input type="number" class="form-control" name="harga_beli" required value="{{ old('harga_beli', $item->harga_beli ?? '') }}">
        @error('harga_beli')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mt-2">
        <label>Laba (dalam persen)</label>
        <input type="number" class="form-control" name="laba" required value="{{ old('laba', $item->laba ?? '') }}">
        @error('laba')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    @php $selectedSupplier = old('supplier', $item->supplier ?? ''); @endphp
    <div class="form-group mt-2">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if($selectedSupplier == '') selected @endif value="">--Pilih--</option>
            <option @if($selectedSupplier == 'Tokopaedi') selected @endif value="Tokopaedi">Tokopaedi</option>
            <option @if($selectedSupplier == 'Bukulapuk') selected @endif value="Bukulapuk">Bukulapuk</option>
            <option @if($selectedSupplier == 'TokoBagas') selected @endif value="TokoBagas">TokoBagas</option>
            <option @if($selectedSupplier == 'E Commurz') selected @endif value="E Commurz">E Commurz</option>
            <option @if($selectedSupplier == 'Blublu') selected @endif value="Blublu">Blublu</option>
        </select>
        @error('supplier')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    @php $selectedJenis = old('jenis', $item->jenis ?? ''); @endphp
    <div class="form-group mt-2">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if($selectedJenis == '') selected @endif value="">--Pilih--</option>
            <option @if($selectedJenis == 'Obat') selected @endif value="Obat">Obat</option>
            <option @if($selectedJenis == 'Alkes') selected @endif value="Alkes">Alkes</option>
            <option @if($selectedJenis == 'Matkes') selected @endif value="Matkes">Matkes</option>
            <option @if($selectedJenis == 'Umum') selected @endif value="Umum">Umum</option>
            <option @if($selectedJenis == 'ATK') selected @endif value="ATK">ATK</option>
        </select>
        @error('jenis')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    @php
        $defaultKategoris = isset($item->kategoriItems)
            ? $item->kategoriItems->pluck('id')->toArray()
            : [];
        $selectedKategoris = old('kategori_ids', $defaultKategoris);
    @endphp
    <div class="form-group mt-2">
        <label>Kategori</label>
        <div class="border rounded p-2">
            @forelse($kategoris as $kat)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           id="kat-{{ $kat->id }}"
                           name="kategori_ids[]"
                           value="{{ $kat->id }}"
                           @checked(in_array($kat->id, $selectedKategoris))>
                    <label class="form-check-label" for="kat-{{ $kat->id }}">
                        {{ $kat->kode }} - {{ $kat->nama }}
                    </label>
                </div>
            @empty
                <small class="text-muted">Belum ada kategori. Tambah di menu Kategori Items.</small>
            @endforelse
        </div>
        @error('kategori_ids')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mt-2">
        <label>Foto</label>
        <input type="file" class="form-control" name="foto" accept="image/*">
        @if(!empty($item->foto))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $item->foto) }}" width="120" class="img-thumbnail" alt="Foto Item">
            </div>
        @endif
        @error('foto')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button class="btn btn-primary mt-3">Submit</button>
</form>