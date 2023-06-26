@extends('layouts.app')

@section('header')
@include('layouts.datetimepicker-header')
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        Selamat datang {{ $role }} {{ Auth::user()->name }}
    </div>
</div>
@include('layouts.flash-message')
<div class="card mb-3">
    <div class="card-body">
        {{-- <form action="{{ route('presence-set.clear') }}" method="post"> --}}
        <form action="#" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-sm-1">
                    <input type="text" class="form-control" name="year" id="year" placeholder="yyyy" value="{{ $year }}" required>
                </div>
                <div class="col-sm-2">
                    <select name="set" class="form-select">
                        <option value="">-</option>
                        <option value="regist">Registrasi</option>
                        <option value="presence">Presensi</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </div>
        </form>
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
@endsection