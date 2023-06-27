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
                                    <option value="{{ $e->id }}" {{ $e->id === $eventSelect ? 'selected' : '' }}>{{ $e->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Acara Wajib Diisi!</div>
                        </div>
                        <div class="col-sm-2 mb-3">
                            <label class="form-label">Divisi</label>
                            <select name="divselect" class="form-select">
                                <option value="">--pilih kelas--</option>
                                @foreach ($division as $d)
                                    <option value="{{ $d->code }}" {{ $d->code === $divSelect ? 'selected' : '' }}>{{ $d->code }}</option>
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
                        <th>Kehadiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8" id="name"></dd>
                    <dt class="col-sm-4">Kelas</dt><dd class="col-sm-8" id="classes"></dd>
                    <dt class="col-sm-4">Tahun</dt><dd class="col-sm-8" id="year"></dd>
                    <dt class="col-sm-4">Kursi Siswa</dt><dd class="col-sm-8" id="seatNumber"></dd>
                    <dt class="col-sm-4">Kursi Orangtua</dt><dd class="col-sm-8" id="seatNumberParent"></dd>
                    <dt class="col-sm-4">User</dt><dd class="col-sm-8" id="user"></dd>
                    <dt class="col-sm-4">Registrasi</dt><dd class="col-sm-8" id="creat"></dd>
                    <dt class="col-sm-4">Update</dt><dd class="col-sm-8" id="update"></dd>
                    <dt class="col-sm-4">Kehadiran</dt><dd class="col-sm-8" id="presence"></dd>
                </dl>
                <form action="" method="post" id="formEdit" novalidate>
                @csrf
                <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Nama Lengkap Orangtua (beserta gelar)<div class="required">*</div></label>
                            <input type="text" class="form-control" name="parent" id="parent" required>
                            <div class="invalid-feedback">Nama Lengkap Orangtua Wajib Diisi!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" type="submit" id="submitEdit">Simpan</button>
                            <div class="spinner-border text-info" role="status" id="loadEdit">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
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
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        responsive: true
    });
});

(function () {
    'use strict';
    const formEdit = document.querySelectorAll('#formEdit');
    Array.prototype.slice.call(formEdit)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitEdit').hide();
            $('#loadEdit').show();
        }
        form.classList.add('was-validated')
      }, false)
    });
})();

var edit = document.getElementById('edit');
edit.addEventListener('show.coreui.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-coreui-id');
    var name = button.getAttribute('data-coreui-name');
    var classes = button.getAttribute('data-coreui-classes');
    var year = button.getAttribute('data-coreui-year');
    var parent = button.getAttribute('data-coreui-parent');
    var seatNumber = button.getAttribute('data-coreui-seatnumber');
    var seatNumberParent = button.getAttribute('data-coreui-seatnumberparent');
    var user = button.getAttribute('data-coreui-user');
    var creat = button.getAttribute('data-coreui-creat');
    var update = button.getAttribute('data-coreui-update');
    var presence = button.getAttribute('data-coreui-presence');
    
    var modalTitle = edit.querySelector('.modal-title');
    var modalBodyId = edit.querySelector('.modal-body #id');
    var modalBodyName = edit.querySelector('.modal-body #name');
    var modalBodyClasses = edit.querySelector('.modal-body #classes');
    var modalBodyYear = edit.querySelector('.modal-body #year');
    var modalBodyParent = edit.querySelector('.modal-body #parent');
    var modalBodySeatNumber = edit.querySelector('.modal-body #seatNumber');
    var modalBodySeatNumberParent = edit.querySelector('.modal-body #seatNumberParent');
    var modalBodyUser = edit.querySelector('.modal-body #user');
    var modalBodyCreat = edit.querySelector('.modal-body #creat');
    var modalBodyUpdate = edit.querySelector('.modal-body #update');
    var modalBodyPresence = edit.querySelector('.modal-body #presence');
    
    modalTitle.textContent = 'Edit data ' + name ;
    modalBodyId.value = id;
    modalBodyName.textContent = ': ' + name;
    modalBodyClasses.textContent = ': ' + classes;
    modalBodyYear.textContent = ': ' + year;
    modalBodyParent.value = parent;
    modalBodySeatNumber.textContent = ': ' + seatNumber;
    modalBodySeatNumberParent.textContent = ': ' + seatNumberParent;
    modalBodyUser.textContent = ': ' + user;
    modalBodyCreat.textContent = ': ' + creat;
    modalBodyUpdate.textContent = ': ' + update;
    modalBodyPresence.textContent = ': ' + presence;
});
</script>
@endsection