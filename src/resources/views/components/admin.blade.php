<header class="header">
    <div class="header__inner">
        <a class="header__logo" href="{{ route('admin.login') }}">
            <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
        </a>
    </div>
    <nav class="header__nav">
        <ul class="nav__list">
            <li class="nav__item"><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
            <li class="nav__item"><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
            <li class="nav__item"><a href="{{ route('admin.correction.list') }}">申請一覧</a></li>
            <li class="nav__item">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            </li>
        </ul>
    </nav>
</header>
<link rel="stylesheet" href="{{ asset('/css/common.css')  }}?v={{ time() }}">