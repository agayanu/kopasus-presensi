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
            <div>Admin</div>
            <button class="btn btn-sm btn-primary" type="button" data-coreui-toggle="modal" data-coreui-target="#add"><i class="cil-plus" style="font-weight:bold"></i>Tambah</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="display nowrap" id="datatable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="formAdd" novalidate>
                @csrf
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Nama Lengkap<div class="required">*</div></label>
                            <input type="text" class="form-control" name="name" required>
                            <div class="invalid-feedback">Nama Lengkap Wajib Diisi!</div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Username<div class="required">*</div></label>
                            <input type="text" class="form-control" name="username" required>
                            <div class="invalid-feedback">Username Wajib Diisi!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" type="submit" id="submitAdd">Tambah</button>
                            <div class="spinner-border text-info" role="status" id="loadAdd">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
                <form action="{{ route('user.update') }}" method="post" id="formEdit" novalidate>
                @csrf
                <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Nama Lengkap<div class="required">*</div></label>
                            <input type="text" class="form-control" name="name" id="name" required>
                            <div class="invalid-feedback">Nama Lengkap Wajib Diisi!</div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Username<div class="required">*</div></label>
                            <input type="text" class="form-control" name="username" id="username" required>
                            <div class="invalid-feedback">Username Wajib Diisi!</div>
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
<div class="modal fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="formReset" novalidate>
                @csrf
                    <h4 class="text-danger" id="resetText"></h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" type="submit" id="submitReset">Ya</button>
                            <div class="spinner-border text-info" role="status" id="loadReset">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="formDel" novalidate>
                @csrf
                    <h4 class="text-danger" id="delText"></h4>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" type="submit" id="submitDel">Ya</button>
                            <div class="spinner-border text-info" role="status" id="loadDel">
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
        ajax: "{{ route('user.data') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'username', name: 'username'},
            {data: 'update', name: 'update'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        responsive: true
    });
});

(function () {
    'use strict';
    const formAdd = document.querySelectorAll('#formAdd');
    const formEdit = document.querySelectorAll('#formEdit');
    const formReset = document.querySelectorAll('#formReset');
    const formDel = document.querySelectorAll('#formDel');
    Array.prototype.slice.call(formAdd)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitAdd').hide();
            $('#loadAdd').show();
        }
        form.classList.add('was-validated')
      }, false)
    });
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
    Array.prototype.slice.call(formReset)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitReset').hide();
            $('#loadReset').show();
        }
        form.classList.add('was-validated')
      }, false)
    });
    Array.prototype.slice.call(formDel)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitDel').hide();
            $('#loadDel').show();
        }
        form.classList.add('was-validated')
      }, false)
    });
})();

var edit = document.getElementById('edit');
var reset = document.getElementById('reset');
var del = document.getElementById('del');
edit.addEventListener('show.coreui.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-coreui-id');
    var name = button.getAttribute('data-coreui-name');
    var username = button.getAttribute('data-coreui-username');
    
    var modalTitle = edit.querySelector('.modal-title');
    var modalBodyId = edit.querySelector('.modal-body #id');
    var modalBodyName = edit.querySelector('.modal-body #name');
    var modalBodyUsername = edit.querySelector('.modal-body #username');
    
    modalTitle.textContent = 'Edit data ' + name;
    modalBodyId.value = id;
    modalBodyName.value = name;
    modalBodyUsername.value = username;
});
reset.addEventListener('show.coreui.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-coreui-id');
    var name = button.getAttribute('data-coreui-name');
    var url = button.getAttribute('data-coreui-url');
    
    var modalTitle = reset.querySelector('.modal-title');
    var modalBodyFormReset = reset.querySelector('.modal-body #formReset');
    var modalBodyResetText = reset.querySelector('.modal-body #resetText');
    
    modalTitle.textContent = 'Reset Password ' + name;
    modalBodyFormReset.action = url;
    modalBodyResetText.textContent = 'Yakin akan mereset password ' + name + ' menjadi 123456 ???';
});
del.addEventListener('show.coreui.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-coreui-id');
    var name = button.getAttribute('data-coreui-name');
    var url = button.getAttribute('data-coreui-url');
    
    var modalTitle = del.querySelector('.modal-title');
    var modalBodyFormDel = del.querySelector('.modal-body #formDel');
    var modalBodyDelText = del.querySelector('.modal-body #delText');
    
    modalTitle.textContent = 'Hapus akun ' + name;
    modalBodyFormDel.action = url;
    modalBodyDelText.textContent = 'Yakin akan menghapus ' + name + ' ???';
});
</script>
@endsection