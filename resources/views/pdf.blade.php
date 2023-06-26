<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('assets/logos/kopasus.png') }}" type="image/x-icon">
    <title>QrCode Kopasus Presensi</title>
    <style>
    @page {
        margin-top:30px;
        margin-bottom:30px;
    }
    table {
        margin-bottom: 20px;
    }
    .logopesat {
        width: 30px;
        margin-bottom: -8px;
    }
    .pesattext {
        font-size: 20px;
        font-weight: bold;
        display: inline;
    }
    .content {
        text-align: center;
    }
    .name-qr {
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 20px;
    }
    .code-qr {
        margin-top: 10px;
        font-weight: bold;
        font-size: 20px;
    }
    .info-group {
        margin-top: 20px;
    }
    .info {
        font-weight: bold;
    }
    </style>
</head>
<body>
<table width="100%">
    <tr>
        <td>
            <img src="{{ asset('assets/logos/kopasus.png') }}" alt="Logo PESAT" class="logopesat">
            <div class="pesattext">KOPASUS PRESENSI</div>
        </td>
        <td style="text-align: right;padding-right: 10px;"><b>QrCode Kehadiran</b></td>
    </tr>
</table>
<div class="content">
    <div class="name-qr">{{ $name }}</div>
    <img src="{{ asset('storage/images/qrcode/'.$id.'.png') }}" alt="QrCode">
    <div class="code-qr">{{ $nrp }}</div>
    <div class="info-group">
        <div class="info">*Simpan QrCode ini untuk Scan Kedatangan dan Pulang saat Acara*</div>
    </div>
</div>
</body>
</html>