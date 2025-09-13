<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4">
        <div class="btn-toggle">
            <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
        </div>
        <div class="search-bar flex-grow-1">
            <div class="position-relative">
                <span
                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 search-close">close</span>
                <div class="search-popup p-3">
                    <div class="card rounded-4 overflow-hidden">
                        <div class="card-header d-lg-none">
                            <div class="position-relative">
                                <input class="form-control rounded-5 px-5 mobile-search-control" type="text"
                                    placeholder="Search">
                                <span
                                    class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50">search</span>
                                <span
                                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 mobile-search-close">close</span>
                            </div>
                        </div>
                        <div class="card-body search-content">
                            <p class="search-title">Recent Searches</p>
                            <hr>
                            <p class="search-title">Tutorials</p>
                            <div class="search-list d-flex flex-column gap-2">
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="list-icon">
                                        <i class="material-icons-outlined fs-5">play_circle</i>
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title ">Wordpress Tutorials</h5>
                                    </div>
                                </div>
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="list-icon">
                                        <i class="material-icons-outlined fs-5">shopping_basket</i>
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title">eCommerce Website Tutorials</h5>
                                    </div>
                                </div>
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="list-icon">
                                        <i class="material-icons-outlined fs-5">laptop</i>
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title">Responsive Design</h5>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p class="search-title">Members</p>
                            <div class="search-list d-flex flex-column gap-2">
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="memmber-img">
                                        <img src="{{ asset('assets/images/avatars/01.png') }}" width="32"
                                            height="32" class="rounded-circle" alt="">
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title ">Andrew Stark</h5>
                                    </div>
                                </div>
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="memmber-img">
                                        <img src="{{ asset('assets/images/avatars/02.png') }}" width="32"
                                            height="32" class="rounded-circle" alt="">
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title ">Snetro Jhonia</h5>
                                    </div>
                                </div>
                                <div class="search-list-item d-flex align-items-center gap-3">
                                    <div class="memmber-img">
                                        <img src="{{ asset('assets/images/avatars/03.png') }}" width="32"
                                            height="32" class="rounded-circle" alt="">
                                    </div>
                                    <div class="">
                                        <h5 class="mb-0 search-list-title">Michle Clark</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center bg-transparent">
                            <a href="javascript:;" class="btn w-100">See All Search Results</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="navbar-nav gap-1 nav-right-links align-items-center">
            <li class="nav-item d-lg-none mobile-search-btn">
                <a class="nav-link" href="javascript:;"><i class="material-icons-outlined">search</i></a>
            </li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
        <span class="fw-bold text-uppercase" style="width:22px; display:inline-block; text-align:center;">
            {{ strtoupper(session('lang', 'ar')) }}
        </span>
    </a>
    <ul class="dropdown-menu {{ app()->getLocale() == 'ar' ? 'dropdown-menu-end' : '' }}">
        <li>
            <a class="dropdown-item" href="#" onclick="setLang('ar'); return false;">
                <span class="fw-bold text-uppercase" style="width:20px; display:inline-block; text-align:center;">AR</span>
                <span>العربية</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="setLang('en'); return false;">
                <span class="fw-bold text-uppercase" style="width:20px; display:inline-block; text-align:center;">EN</span>
                <span>English</span>
            </a>
        </li>
    </ul>
</li>
            

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                    data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:;"><i
                        class="material-icons-outlined">notifications</i>
                    @php
                        use App\Models\Notification;
                    @endphp
                    <span class="{{ Notification::countUnreadMessages(auth()->id()) > 0 ? 'badge-notify' : '' }}"
                        id='countUnreadMessages'>{{ Notification::countUnreadMessages(auth()->id()) > 0 ? Notification::countUnreadMessages(auth()->id()) : '' }}</span>
                </a>
                <div class="dropdown-menu  loder-notify dropdown-notify dropdown-menu-end shadow">
                    <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
                        <h5 class="notiy-title mb-0">Notifications</h5>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret option"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-icons-outlined">
                                    more_vert
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
                                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="javascript:;"><i
                                            class="material-icons-outlined fs-6">inventory_2</i>Archive All</a></div>
                                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="javascript:;"><i class="material-icons-outlined fs-6">done_all</i>Mark
                                        all as read</a></div>
                                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="javascript:;"><i class="material-icons-outlined fs-6">mic_off</i>Disable
                                        Notifications</a></div>
                                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="javascript:;"><i class="material-icons-outlined fs-6">grade</i>What's
                                        new ?</a></div>
                                <div>
                                    <hr class="dropdown-divider">
                                </div>
                                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                        href="javascript:;"><i
                                            class="material-icons-outlined fs-6">leaderboard</i>Reports</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="notify-list loder-notify " id="notificationsList">
                        @foreach (auth()->user()->notifications->sortByDesc('created_at')->take(10) as $notification)
                            <div>
                                <a class="dropdown-item border-bottom py-2 notification-item {{ $notification->read == 0 ? 'unread-notification' : '' }}"
                                    href="javascript:;" data-id="{{ $notification->id }}">
                                    <div class="d-flex align-items-center gap-3 position-relative">
                                        <div class="position-relative">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->sender->name) }}&background=random"
                                                width="45" height="45"
                                                alt="{{ $notification->sender->name }}" class="rounded-circle">
                                            @if($notification->read == 0)
                                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle notification-dot">
                                                    <span class="visually-hidden">New alerts</span>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            @php
                                                $lang = session('lang' , 'ar');
                                                $title = $lang == 'ar' ? $notification->title_ar : $notification->title_en;
                                                $body = $lang == 'ar' ? $notification->body_ar : $notification->body_en;
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5 class="notify-title mb-1 {{ $notification->read == 0 ? 'fw-bold' : '' }}">{{ $title }}</h5>
                                                @if($notification->read == 0)
                                                    <span class="badge bg-primary rounded-pill notification-badge">جديد</span>
                                                @endif
                                            </div>
                                            <p class="mb-1 notify-desc {{ $notification->read == 0 ? 'text-dark fw-medium' : 'text-muted' }}">{{ $body }}</p>
                                            <p class="mb-0 notify-time text-muted small">
                                                <i class="material-icons-outlined fs-6 me-1">access_time</i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        {{-- <div class="notify-close position-absolute end-0 top-50 translate-middle-y me-2">
                                            <i class="material-icons-outlined fs-6 text-muted">close</i>
                                        </div> --}}
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Loading indicator -->
                    <div class="notification-loading d-none text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2 text-muted small">Loading more notifications...</span>
                    </div>

                    <!-- No more notifications message -->
                    <div class="no-more-notifications d-none text-center py-3 text-muted small">
                        <i class="material-icons-outlined fs-6 me-1">check_circle</i>
                        No more notifications
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                        class="rounded-circle p-1 border" width="45" height="45" alt="">
                </a>
                <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
                    <a class="dropdown-item gap-2 py-2" href="javascript:;">
                        <div class="text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                                class="rounded-circle p-1 shadow mb-3" width="90" height="90" alt="">
                            <h5 class="user-name mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('profile.show', ['id' => Auth::user()->id]) }}">
                        <i class="material-icons-outlined">person_outline</i>Profile
                    </a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                        href="{{ route('profile.show', ['id' => Auth::user()->id]) }}">
                        <i class="material-icons-outlined">local_bar</i>Settings
                    </a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('dashboard') }}">
                        <i class="material-icons-outlined">dashboard</i>Dashboard
                    </a>
                    <hr class="dropdown-divider">
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
                            <i class="material-icons-outlined">power_settings_new</i>Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
</header>
<script>
      function setLang(lang) {
        localStorage.setItem('lang', lang);
        localStorage.setItem('dir', lang === 'ar' ? 'rtl' : 'ltr');

        $.ajax({
            url: '/set-lang',
            method: 'POST',
            data: { 
                lang: lang,
                dir: lang === 'ar' ? 'rtl' : 'ltr'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                // Update the HTML direction attribute
                document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
                document.documentElement.setAttribute('lang', lang);
                document.body.className = document.body.className.replace(/\b(rtl|ltr)\b/g, '') + ' ' + (lang === 'ar' ? 'rtl' : 'ltr');
                
                // Reload the page to reflect the language change
                location.reload();
            },
            error: function() {
                alert('There was an error changing the language. Please try again.');
            }
        });
    }

    // Set initial direction on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedLang = localStorage.getItem('lang') || '{{ app()->getLocale() }}';
        const savedDir = localStorage.getItem('dir') || (savedLang === 'ar' ? 'rtl' : 'ltr');
        
        document.documentElement.setAttribute('dir', savedDir);
        document.documentElement.setAttribute('lang', savedLang);
        document.body.className = document.body.className.replace(/\b(rtl|ltr)\b/g, '') + ' ' + savedDir;
    });
</script>
