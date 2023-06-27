@extends('layouts.app')

@section('header')
<style>
#loadReader {
    width: 5rem;
    height: 5rem;
}
.spinner-border {
    display: none;
}
#reader {
    width: 300px;
}
</style>
@endsection

@section('content')
@include('layouts.flash-message')
@if ($message = Session::get('errorx'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Gagal!!!</strong>
    <ul>
        @foreach ($message->all() as $m)
            <li>{{ $m }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="card mb-3">
    <div class="card-header">
        <a href="{{ route('presence') }}">Kehadiran</a> / Scan
    </div>
    <div class="card-body" style="text-align: -webkit-center;">
        <div id="reader"></div>
        <div class="spinner-border text-info" role="status" id="loadReader">
            <span class="visually-hidden">Loading...</span>
        </div>
        <hr>
        <div class="or">atau</div>
        <hr>
        <form action="" method="post" id="formCode">
            @csrf
            <div class="row justify-content-center">
                <div class="col-sm-3">
                    <input type="text" name="codeqr" class="form-control mb-3" placeholder="Masukan Kode QR ...">
                    <button type="submit" class="btn btn-primary" id="submitCode">Proses</button>
                    <div class="spinner-border text-info" role="status" id="loadCode">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer')
<script src="{{ asset('assets/jquery/jquery-3.6.1.min.js') }}"></script>
<script src="{{ asset('assets/html5-qrcode/html5-qrcode.min.js') }}"></script>
<script>
(function () {
    'use strict';
    const formCode = document.querySelectorAll('#formCode');
    Array.prototype.slice.call(formCode)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitCode').hide();
            $('#loadCode').css('display', 'inline-block');
        }
        form.classList.add('was-validated')
      }, false)
    });
})();

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code matched = ${decodedText}`, decodedResult);
    window.location.href = "{{ route('presence.presence', ['key' => ".d."]) }}".replace(".d.", decodedText);
    html5QrcodeScanner.clear().then(_ => {
        // the UI should be cleared here      
    }).catch(error => {
        console.warn(`Code scan error = ${error}`);
    });
    $('#reader').hide();
    $('#loadReader').show();
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    if(error != 'QR code parse error, error = D: No MultiFormat Readers were able to detect the code.') {
        console.warn(`Code scan error = ${error}`);
    }
}

let html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: {width: 285, height: 285} },
  /* verbose= */ false);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection