<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('storage/logos/favicon.png') }}" type="image/x-icon">
    <title>Regist - Kopasus Presensi</title>
    <link rel="stylesheet" href="{{ asset('storage/coreui/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/coreui/icons/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/select2-4.1.0-rc.0/css/select2.min.css') }}">
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
        /* begin select2 */
        span.select2 {
            width: 100% !important;
        }
        span.select2-selection {
            min-height: 38px !important;
        }
        span.select2-selection__rendered {
            padding: 0.275rem 2.25rem 0.375rem 0.75rem !important;
        }
        .select2-results__option--selected  {
            display: none;
        }
        /* end select2 */
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
            <div class="col-sm-8 col-lg-6 col-xl-4">
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
                                <b>Registrasi</b>
                            </div>
                        </div>
                        <form action="" method="POST" id="formRegist">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 mb-3">
                                    <label class="form-label">Nama Siswa</label>
                                    <select name="student" id="student" class="form-select @error('student') is-invalid @enderror" required></select>
                                    @error('student')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label class="form-label">Nama Lengkap Orangtua (beserta gelar)</label>
                                    <input class="form-control @error('parent') is-invalid @enderror" type="text" name="parent" value="{{ old('parent') }}" required>
                                    @error('parent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-primary" id="submitRegist">Daftar</button>
                                    <div class="spinner-border text-info" role="status" id="loadRegist">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </form>
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
(function () {
    'use strict';
    const formRegist = document.querySelectorAll('#formRegist');
    Array.prototype.slice.call(formRegist)
    .forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        
        if (form.checkValidity() === true) {
            $('#submitRegist').hide();
            $('#loadRegist').css('display', 'inline-block');
        }
        form.classList.add('was-validated')
      }, false)
    });
})();
</script>
<script src="{{ asset('storage/select2-4.1.0-rc.0/js/select2.min.js') }}"></script>
<script>
$("#student").select2({
    ajax: {
        url: "{{ route('regist.student') }}",
        type: 'GET',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                term: params.term,
                page: params.current_page
            };
        },
        processResults: function(data, params) {
            params.current_page = params.current_page || 1;
            return {
                results: data[0].data,
                pagination: {
                    more: (params.current_page * 30) < data[0].total
                }
            };
        },
        autoWidth: true,
        cache: true
    },
    minimumInputLength: 1,
    templateResult: formatName,
    templateSelection: formatNameSelection,
    allowClear: true,
    placeholder: 'Cari ...'
});
function formatName(name) {
    if (name.loading) {
        return name.text;
    }

    var $container = $(
        "<div class='select2-result-name clearfix'>" +
        "<div class='select2-result-name__name'></div>" +
        "<div class='select2-result-name__class'></div>" +
        "</div>" +
        "</div>" +
        "</div>"
    );

    $container.find(".select2-result-name__name").text(name.name);
    $container.find(".select2-result-name__class").text(name.class);

    return $container;
}
function formatNameSelection(name) {
    return name.name || name.text;
}
$(document).on('select2:open', () => {
    let allFound = document.querySelectorAll('.select2-container--open .select2-search__field');
    $(this).one('mouseup keyup',()=>{
        setTimeout(()=>{
            allFound[allFound.length - 1].focus();
        },0);
    });
});
</script>
</body>
</html>