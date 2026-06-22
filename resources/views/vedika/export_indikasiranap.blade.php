<html>

<head></head>

<body>
    <table style="border: 1px;">
        <thead>
            <tr>
                <th>No RM</th>
                <th>Nama</th>
                <th>No SEP</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Pulang</th>
                <th>DPJP</th>
                <th>Indikasi Dirawat</th>
                <th>Keluhan Utama</th>
                <th>TTV</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $list)
                <tr>
                    <td class="align-middle">{{ $list->no_rm }}</td>
                    <td class="align-middle">{{ $list->nama_pasien }}</td>
                    <td class="align-middle">{{ $list->no_sep }} </td>
                    <td class="align-middle">{{ $list->tgl_masuk }}</td>
                    <td class="align-middle">{{ $list->tgl_keluar }}</td>
                    <td class="align-middle">{{ $list->dpjp }}</td>
                    <td class="align-middle">{{ $list->indikasi }}</td>
                    <td class="align-middle">{{ $list->keluhan }}</td>
                    <td class="align-middle">TD: {{ $list->tensi ?? '-' }}, N:{{ $list->nadi ?? '-' }}, R:
                        {{ $list->respirasi ?? '-' }}, S: {{ $list->suhu ?? '-' }}, SPO2: {{ $list->spo ?? '-' }},
                        BB: {{ $list->bb ?? '-' }}, TB: {{ $list->tb ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
