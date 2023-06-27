@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        Selamat datang {{ $role }} {{ Auth::user()->name }}
    </div>
</div>
@include('layouts.flash-message')
<div class="card mb-3">
    <div class="card-body">
        <div class="mb-3 text-center"><h2>HAPUS REGISTRASI/PRESENSI</h2></div>
        <form action="{{ route('presence-set.clear') }}" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-sm-2">
                    <select name="gen" class="form-select" required>
                        <option value="">--Pilih Angkatan--</option>
                        @foreach($gen as $g)
                            <option value="{{ $g->gen }}">{{ $g->gen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="set" class="form-select" required>
                        <option value="">--Pilih Jenis--</option>
                        <option value="regist">Registrasi</option>
                        <option value="presence">Presensi</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="event" class="form-select" required>
                        <option value="">--Pilih Acara--</option>
                        @foreach($event as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                        @endforeach
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
