<li class="nav-item">
    <a class="nav-link logout" href="{!! route('logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="nav-icon cil-account-logout logout"></i> Logout
    </a>
</li>
<li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('home') !!}">
        <i class="nav-icon cil-home"></i> Beranda
    </a>
</li>
<li class="nav-item {{ Request::is('change-pass') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('change-pass') !!}">
        <i class="nav-icon cil-home"></i> Ganti Password
    </a>
</li>
<li class="nav-title">Menu</li>
<li class="nav-item {{ Request::is('presence') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('presence') !!}">
        <i class="nav-icon cil-home"></i> Kehadiran
    </a>
</li>
<li class="nav-item {{ Request::is('regist-not') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('regist-not') !!}">
        <i class="nav-icon cil-home"></i> Belum Registrasi
    </a>
</li>
<li class="nav-item {{ Request::is('user') ? 'active' : '' }}">
    <a class="nav-link" href="{!! route('user') !!}">
        <i class="nav-icon cil-home"></i> Admin
    </a>
</li>