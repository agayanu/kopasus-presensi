@extends('layouts.app')

@section('header')
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
<div class="row justify-content-center">
    <div class="col-sm-5">
        <div class="card mb-3">
            <div class="card-header">
                <div>Ganti Password</div>
            </div>
            <div class="card-body">
                <form action="" method="post" id="formChange" novalidate>
                @csrf
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Password Baru<div class="required">*</div></label>
                            <div class="input-group has-validation">
                                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="Password" value="{{ old('password') }}">
                                <span class="input-group-text" id="span-lock">
                                    <i class="icon cil-lock-locked" id="span-lock-i"></i>
                                </span>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label">Ulangi Password Baru<div class="required">*</div></label>
                            <div class="input-group has-validation">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" id="passwordConfirm" placeholder="Konfirmasi Password" value="{{ old('password_confirmation') }}">
                                <span class="input-group-text" id="span-lock-confirm">
                                    <i class="icon cil-lock-locked" id="span-lock-i-confirm"></i>
                                </span>
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" type="submit" id="submitChange">Ganti</button>
                            <div class="spinner-border text-info" role="status" id="loadChange">
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
<script>
(function () {
    'use strict';
    const formChange = document.querySelectorAll('#formChange');
    Array.prototype.slice.call(formChange)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitChange').hide();
            $('#loadChange').show();
        }
        form.classList.add('was-validated')
      }, false)
    });
})();

$('#span-lock').click(function() {
    var input = $('#password').attr('type');
    if(input == 'text') {
        $('#password').attr('type', 'password');
        $('#span-lock-i').addClass('cil-lock-locked');
        $('#span-lock-i').removeClass('cil-lock-unlocked');
    } else {
        $('#password').attr('type', 'text');
        $('#span-lock-i').addClass('cil-lock-unlocked');
        $('#span-lock-i').removeClass('cil-lock-locked');
    }
});
$('#span-lock-confirm').click(function() {
    var input = $('#passwordConfirm').attr('type');
    if(input == 'text') {
        $('#passwordConfirm').attr('type', 'password');
        $('#span-lock-i-confirm').addClass('cil-lock-locked');
        $('#span-lock-i-confirm').removeClass('cil-lock-unlocked');
    } else {
        $('#passwordConfirm').attr('type', 'text');
        $('#span-lock-i-confirm').addClass('cil-lock-unlocked');
        $('#span-lock-i-confirm').removeClass('cil-lock-locked');
    }
});
</script>
@endsection