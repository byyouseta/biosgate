<html lang="en">

<head>
    <title>Pencarian Autocomplete di Laravel Menggunakan Ajax</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
</head>

<body>

    <div class="container">
        <h2>Pencarian Autocomplete di Laravel Menggunakan Ajax</h2>
        <br />
        <select class="cari form-control" style="width:500px;" name="cari"></select>
    </div>
    <div>
        <p>Taken from wikpedia</p>
        <img src="data:image/png;base64,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"
            alt="Red dot" />
    </div>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $('.cari').select2({
            placeholder: 'Cari...',
            ajax: {
                url: '/geticd10',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.nama_penyakit,
                                id: item.kd_penyakit
                            }
                        })
                    };
                },
                cache: true
            }
        });
    </script>
</body>

</html>
