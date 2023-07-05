@extends('layouts.app')

@section('header')
@include('layouts.header-datatable')
@include('layouts.datetimepicker-header')
@include('layouts.tooltips')
<style>
.required {
    color: red;
    display: inline;
}
.spinner-border {
    display: none;
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
        Belum Registrasi
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-10">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-sm-2 mb-3">
                            <label class="form-label">Acara</label>
                            <select name="eventselect" id="" class="form-select">
                                @foreach($event as $e)
                                    <option value="{{ $e->id }}" {{ $e->id == $eventSelect ? 'selected' : '' }}>{{ $e->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Acara Wajib Diisi!</div>
                        </div>
                        <div class="col-sm-2 mb-3">
                            <label class="form-label">Divisi</label>
                            <select name="divselect" class="form-select">
                                <option value="">--pilih kelas--</option>
                                @foreach ($division as $d)
                                    <option value="{{ $d->code }}" {{ $d->code == $divSelect ? 'selected' : '' }}>{{ $d->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2 mb-3 align-self-end">
                            <button class="btn btn-sm btn-primary">Tampil</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-2 align-self-end mb-3">
                <form action="{{ route('regist-not.download') }}" method="get" style="text-align: right">
                    <input type="hidden" name="event_d" value="{{ $eventSelect }}">
                    <input type="hidden" name="div_d" value="{{ $divSelect }}">
                    <button class="btn btn-sm btn-success tooltips" type="submit">
                        <i class="cil-cloud-download" style="font-weight: bold;font-size: 20px;"></i> Excel
                        <span class="tooltiptext">Download Excel</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display nowrap" id="datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Divisi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script src="{{ asset('assets/jquery/jquery-3.6.1.min.js') }}"></script>
@include('layouts.datetimepicker-footer')
<script>
$('#year').datetimepicker({
    locale: 'id',
    format: 'YYYY'
});
</script>
@include('layouts.footer-datatable')
<script>
$(function () {
    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('regist-not.data', ['eventselect'=>$eventSelect,'divselect'=>$divSelect]) }}".replace(/&amp;/g, "&"),
        columns: [
            {data: 'name', name: 'name'},
            {data: 'class', name: 'class'},
            {data: 'division', name: 'division'},
        ],
        responsive: true
    });
});
</script>
@endsection