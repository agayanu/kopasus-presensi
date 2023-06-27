@extends('layouts.app')

@section('header')
@include('layouts.header-datatable')
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
        <div class="d-flex flex-row justify-content-between">
            <div>Kehadiran</div>
            <div>
                <a href="{{ route('presence.scan') }}" class="btn btn-sm btn-primary">Scan</a>
                <a href="{{ route('presence.show') }}" class="btn btn-sm btn-primary">Tampilan</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-10">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-sm-2 mb-3">
                            <label class="form-label">Acara</label>
                            <select name="eventselect" id="" class="form-select">
                                <option value="">--Pilih--</option>
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
                <form action="{{ route('presence.download') }}" method="get" style="text-align: right">
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
                        <th>Divisi</th>
                        <th>Acara</th>
                        <th>Datang</th>
                        <th>Pulang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8" id="name"></dd>
                    <dt class="col-sm-4">Kelas</dt><dd class="col-sm-8" id="classes"></dd>
                    <dt class="col-sm-4">Divisi</dt><dd class="col-sm-8" id="division"></dd>
                    <dt class="col-sm-4">Acara</dt><dd class="col-sm-8" id="event"></dd>
                    <dt class="col-sm-4">User</dt><dd class="col-sm-8" id="user"></dd>
                    <dt class="col-sm-4">Registrasi</dt><dd class="col-sm-8" id="create"></dd>
                    <dt class="col-sm-4">Datang</dt><dd class="col-sm-8" id="presence"></dd>
                    <dt class="col-sm-4">Pulang</dt><dd class="col-sm-8" id="home"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script src="{{ asset('assets/jquery/jquery-3.6.1.min.js') }}"></script>
@include('layouts.footer-datatable')
<script>
$(function () {
    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('presence.data', ['eventselect'=>$eventSelect,'divselect'=>$divSelect]) }}".replace(/&amp;/g, "&"),
        columns: [
            {data: 'name', name: 'name'},
            {data: 'division', name: 'division'},
            {data: 'event', name: 'event'},
            {data: 'presence', name: 'presence'},
            {data: 'home', name: 'home'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        responsive: true
    });
});

var show = document.getElementById('show');
show.addEventListener('show.coreui.modal', function (event) {
    var button = event.relatedTarget;
    var name = button.getAttribute('data-coreui-name');
    var division = button.getAttribute('data-coreui-division');
    var classes = button.getAttribute('data-coreui-class');
    var events = button.getAttribute('data-coreui-event');
    var user = button.getAttribute('data-coreui-user');
    var creates = button.getAttribute('data-coreui-create');
    var presence = button.getAttribute('data-coreui-presence');
    var home = button.getAttribute('data-coreui-home');
    
    var modalTitle = show.querySelector('.modal-title');
    var modalBodyName = show.querySelector('.modal-body #name');
    var modalBodyDivision = show.querySelector('.modal-body #division');
    var modalBodyClass = show.querySelector('.modal-body #classes');
    var modalBodyEvent = show.querySelector('.modal-body #event');
    var modalBodyUser = show.querySelector('.modal-body #user');
    var modalBodyCreate = show.querySelector('.modal-body #create');
    var modalBodyPresence = show.querySelector('.modal-body #presence');
    var modalBodyHome = show.querySelector('.modal-body #home');
    
    modalTitle.textContent = 'Detail data ' + name ;
    modalBodyName.textContent = ': ' + name;
    modalBodyDivision.textContent = ': ' + division;
    modalBodyClass.textContent = ': ' + classes;
    modalBodyEvent.textContent = ': ' + events;
    modalBodyUser.textContent = ': ' + user;
    modalBodyCreate.textContent = ': ' + creates;
    modalBodyPresence.textContent = ': ' + presence;
    modalBodyHome.textContent = ': ' + home;
});
</script>
@endsection