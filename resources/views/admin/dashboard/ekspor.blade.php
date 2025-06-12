<!DOCTYPE html>
<html lang="en">
<head>
  <title>Ekspor Data</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Tambahkan ini di bagian head atau sebelum tag </body> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

</head>
<body>
    <div class="text-center m-5">
        Unduh data mungkin akan membutuhkan waktu yang lama. Harap tunggu hingga data terunduh secara otomatis!<br>
        <span class="text-danger">*Jika tidak otomatis mengunduh data excel tekan tombol dibawah!</span><br><br>
        <button id="export-button" class="btn btn-success">Export ke Excel</button>
    </div>
    @php
    $no = 1;
    @endphp

    <div class="table-responsive">
        <table id="example2" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No. Order</th>
                    <th>Kode Sampel</th>
                    <th>Nama Pelanggan</th>
                    <th>Kegiatan</th>
                    <th>Status Order</th>
                </tr>
            </thead>

            <tbody>

                @foreach($all_data as $dt)
                <tr>
                    <td><button style="padding: 6px 12px 6px 12px; margin-left:12px;" class="btn btn-secondary btn-sm">{{ $no++ }}</button></td>
                    <td>{{ date('d/m/Y', strtotime($dt->created_at)) }}</td>
                    <td>{{ $dt->no_order }}</td>
                    <td>{{ $dt->kode_sampel }}</td>
                    <td>@if($dt->user != null){{ $dt->user->name }} @else User Dihapus @endif</td>
                    <td>{{ $dt->kegiatan }}</td>
                    <td>
                        <i class="
                        @if($dt->status_bayar=='Sudah Dibayar') text-success 
                        @elseif($dt->status_bayar=='Belum Dibayar') text-danger 
                        @elseif($dt->status_bayar=='Ditolak') text-danger 
                        @else text-primary 
                        @endif">
                            <u><strong>{{ $dt->status_bayar }}</strong></u></i>
                        <br>
                        @if($dt->status_bayar=='Ditolak')
                        <i>
                            <small>
                                <strong>Catatan:</strong>
                                {{ $dt->catatan }}
                            </small>
                        </i>
                        @endif

                    </td>

                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
    
    <script>
        // Fungsi untuk mengonversi tabel HTML ke format Excel dan mengunduhnya sebagai file
        /*
        function exportToExcel() {
            var table = document.getElementById('example2');
            var html = table.outerHTML;
            var blob = new Blob([html], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'data.xlsx'; // Nama file Excel yang akan diunduh
            a.click();
            URL.revokeObjectURL(url);
        }*/
    
        // Menambahkan event listener ke tombol
        //document.getElementById('export-button').addEventListener('click', exportToExcel);
    </script>
    <script>
        document.getElementById('export-button').addEventListener('click', function () {
            exportTableToExcel('example2');
        });
    
        function exportTableToExcel(tableId) {
            var table = document.getElementById(tableId);
            var wb = XLSX.utils.table_to_book(table);
            var wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'binary' });
    
            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }
    
            saveAs(new Blob([s2ab(wbout)], { type: 'application/octet-stream' }), 'table_export.xlsx');
        }
    </script>
</body>
</html>