<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('storage/logos/favicon.png') }}" type="image/x-icon">
    <title>Pesat Pelepasan - SMA Plus PGRI Cibinong</title>
    <link rel="stylesheet" href="{{ asset('storage/coreui/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/coreui/icons/css/all.min.css') }}">
    <style>
        .login-bg-container {
            background-image: url({{ asset('storage/images/bg.jpg') }});
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            box-shadow: inset 0 0 0 2000px rgb(16 16 16 / 28%);
            transform: scale(1.1);
            -webkit-transform: scale(1.1);
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
        }
        .logopesat {
            width: 30px;
            margin-bottom: 5px;
        }
        .pesattext {
            font-size: 20px;
            font-weight: bold;
            font-family: fantasy;
            background: -webkit-linear-gradient(#eee, #333);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .spinner-border {
            display: none;
        }
        a {
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .pesattext {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
<div class="login-bg-container"></div>
<div class="min-vh-100 d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8 col-lg-6 col-xl-10">
            @include('layouts.flash-message')
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-8">
                                <div class="tmplogo">
                                    <img src="{{ asset('storage/logos/favicon.png') }}" alt="Logo PESAT" class="logopesat">
                                    <font class="pesattext">PESAT PELEPASAN</font>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <b>10 Kehadiran Terakhir</b>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Orangtua</th>
                                        <th>Nomor Kursi</th>
                                        <th>Nomor Kursi Orangtua</th>
                                        <th>Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="https://smapluspgri.sch.id/">PESAT</a> © 2023 Departemen TIK
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('storage/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('storage/jquery/jquery-3.6.1.min.js') }}"></script>
<script>
setInterval(function () {
    $.get("{{ route('presence.show-data') }}", function(response){
        var len = 0;
        $('tbody').empty();
        if(response['data'] != null){
            len = response['data'].length;
        }
        if(len > 0){
            for(var i=0; i<len; i++){
                var name = response['data'][i].name;
                var classes = response['data'][i].class;
                var parent = response['data'][i].parent;
                var seatNumber = response['data'][i].seatNumber;
                var seatNumberParent = response['data'][i].seatNumberParent;
                var presence = response['data'][i].presence;

                var tr_str = '<tr>' +
                    '<td>' + name + '</td>' +
                    '<td>' + classes + '</td>' +
                    '<td>' + parent + '</td>' +
                    '<td>' + seatNumber + '</td>' +
                    '<td>' + seatNumberParent + '</td>' +
                    '<td>' + presence + '</td>' +
                '</tr>';

                $("tbody").append(tr_str);
            }
        }else{
            var tr_str = '<tr><td colspan="6" class="text-center">Data tidak ditemukan!</td></tr>';

            $("tbody").append(tr_str);
        }
    });
}, 1000);
</script>
</body>
</html>