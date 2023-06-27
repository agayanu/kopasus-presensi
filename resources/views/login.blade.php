<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/logos/kopasus.png') }}" type="image/x-icon">
    <title>Login - Kopasus Presensi</title>
    <link rel="stylesheet" href="{{ asset('assets/coreui/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/coreui/icons/css/all.min.css') }}">
    <style>
        .login-bg-container {
            background-image: url({{ asset('assets/images/bg.jpg') }});
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
        .tmplogo {
            margin-top: 10px;
        }
        .logopesat {
            width: 90px;
            margin-bottom: 15px;
        }
        .pesattext {
            font-size: 28px;
            font-weight: bold;
            font-family: fantasy;
            background: -webkit-linear-gradient(#eee, #333);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .submit {
            text-align: right;
        }
        #span-lock {
            cursor: pointer;
        }
        a {
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .tmplogo {
                /* text-align: center; */
            }
            .logopesat {
                width: 50px;
            }
            .pesattext {
                font-size: 15px;
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
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{!! $message !!}</strong>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{!! $message !!}</strong>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Silahkan cek kembali isian form anda</strong>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
                <div class="card">
                    <div class="card-body">
                        <div class="tmplogo">
                            <img src="{{ asset('assets/logos/kopasus.png') }}" alt="Logo PESAT" class="logopesat">
                            <font class="pesattext">KOPASUS PRESENSI</font>
                        </div>
                        Sekolahnya Para Kader Bangsa
                        <form action="" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 mb-3 mt-3">
                                    <div class="input-group has-validation">
                                        <span class="input-group-text">
                                            <i class="icon cil-user"></i>
                                        </span>
                                        <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" placeholder="Username" value="{{ old('username') }}" autofocus>
                                        @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="span-lock">
                                            <i class="icon cil-lock-locked" id="span-lock-i"></i>
                                        </span>
                                        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" autocomplete="on" placeholder="Password" value="{{ old('password') }}">
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="form-check">
                                        <input type="checkbox" name="remember" class="form-check-input">
                                        <label class="form-check-label">Ingatkan saya</label>
                                    </div>
                                </div>
                                <div class="col submit">
                                    <button type="submit" class="btn btn-primary">Login</button>
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
<script src="{{ asset('assets/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('assets/jquery/jquery-3.6.1.min.js') }}"></script>
<script>
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
</script>
</body>
</html>