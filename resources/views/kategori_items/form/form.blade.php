<form method="POST">
    @csrf
    <div class="form-group">
        <label>Kode</label>
        <input type="text" class="form-control @error('kode') is-invalid @enderror" name="kode" required value="{{ old('kode', $item->kode ?? '') }}">
        @error('kode')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group mt-2">
        <label>Nama</label>
        <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" required value="{{ old('nama', $item->nama ?? '') }}">
        @error('nama')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>
