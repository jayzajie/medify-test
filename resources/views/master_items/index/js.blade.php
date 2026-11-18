<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'desc']],
        });
        getData();
    });

    $('.btn-get-data').click(function() {
        getData();
    });

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function getData() {
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val();
        var filter_nama = $('#filter-nama').val();
        var filter_harga_min = $('#filter-harga-min').val();
        var filter_harga_max = $('#filter-harga-max').val();
        
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{ url("master-items/search") }}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: filter_kode,
                nama: filter_nama,
                hargamin: filter_harga_min,
                hargamax: filter_harga_max
            },
            success: function(results) {
                var data = results.data;

                $.each(data, function(index, item) {
                    var harga_jual = Math.round(parseFloat(item.harga_beli) + (parseFloat(item.harga_beli) * parseFloat(item.laba) / 100));
                    var html = `<a href="{{ url('master-items/view') }}/${encodeURIComponent(item.kode)}" class="btn btn-primary">View</a>`;
                    var kategoriHtml = item.kategori_items && item.kategori_items.length > 0
                        ? item.kategori_items.map(k => escapeHtml(k.nama)).join(', ')
                        : '-';
                    var fotohtml = item.foto
                        ? `<img src="{{ asset('storage') }}/${escapeHtml(item.foto)}" width="60" class="img-thumbnail" />`
                        : '-';

                    var array_temp = [
                        escapeHtml(item.kode),
                        escapeHtml(item.nama),
                        escapeHtml(item.jenis),
                        kategoriHtml,
                        item.harga_beli,
                        harga_jual,
                        escapeHtml(item.supplier),
                        fotohtml,
                        html
                    ];
                    
                    dataTableObj.row.add(array_temp);
                });

                dataTableObj.draw();
                $('#loading-filter').hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data');
                $('#loading-filter').hide();
            }
        });
    }
</script>
