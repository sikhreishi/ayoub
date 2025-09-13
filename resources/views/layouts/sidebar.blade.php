<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="logo-icon">
            <!-- <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-img" alt="Maxton Logo"> -->
        </div>
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0">Ayoub</h5>
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav">
        <ul class="metismenu" id="sidenav">
            @can('view_dashboard')
                <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">dashboard</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.dashboard') }}</div>
                    </a>
                </li>
            @endcan

            @can('view_users')
                <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">people</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.user_management') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.users.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index') }}">
                                <i class="material-icons-outlined">arrow_right</i>{{ __('dashboard.sidebar.all_users') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_drivers')
                <li class="{{ request()->routeIs('admin.drivers.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">drive_eta</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.driver_management') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.drivers.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.index') }}">
                                <i class="material-icons-outlined">arrow_right</i>{{ __('dashboard.sidebar.all_drivers') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.unverified.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.unverified.index') }}">
                                <i class="material-icons-outlined">pending</i>{{ __('dashboard.sidebar.pending_verification') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.available.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.available.index') }}">
                                <i class="material-icons-outlined">check_circle</i>{{ __('dashboard.sidebar.available_drivers') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.unavailable.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.unavailable.index') }}">
                                <i class="material-icons-outlined">cancel</i>{{ __('dashboard.sidebar.unavailable_drivers') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_trips')
                <li class="{{ request()->routeIs('admin.trips.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">local_taxi</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.trip_management') }}</div>
                    </a>
                    <ul>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'pending' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'pending']) }}">
                                <i class="material-icons-outlined">schedule</i>{{ __('dashboard.sidebar.pending_trips') }}
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'accepted' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'accepted']) }}">
                                <i class="material-icons-outlined">thumb_up</i>{{ __('dashboard.sidebar.accepted_trips') }}
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'in_progress' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'in_progress']) }}">
                                <i class="material-icons-outlined">directions</i>{{ __('dashboard.sidebar.in_progress') }}
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'completed' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'completed']) }}">
                                <i class="material-icons-outlined">done_all</i>{{ __('dashboard.sidebar.completed_trips') }}
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'cancelled' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'cancelled']) }}">
                                <i class="material-icons-outlined">clear</i>{{ __('dashboard.sidebar.cancelled_trips') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_vehicle_types')
                <li class="{{ request()->routeIs('admin.vehicle_types.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">directions_car</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.vehicle_management') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.vehicle_types.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.vehicle_types.index') }}">
                                <i class="material-icons-outlined">category</i>{{ __('dashboard.sidebar.vehicle_types') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_countries')
                <li
                    class="{{ request()->routeIs('admin.countries.*') || request()->routeIs('admin.cities.*') || request()->routeIs('admin.districts.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">map</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.location_management') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.countries.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.countries.index') }}">
                                <i class="material-icons-outlined">public</i>{{ __('dashboard.sidebar.countries') }}
                            </a>
                        </li>
                        @can('view_cities')
                            <li class="{{ request()->routeIs('admin.cities.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.cities.index') }}">
                                    <i class="material-icons-outlined">location_city</i>{{ __('dashboard.sidebar.cities') }}
                                </a>
                            </li>
                        @endcan
                        @can('view_districts')
                            <li class="{{ request()->routeIs('admin.districts.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.districts.index') }}">
                                    <i class="material-icons-outlined">place</i>{{ __('dashboard.sidebar.areas') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('view_contacts')
                <li class="{{ request()->routeIs('admin.contacts.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">contacts</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.contact_management') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.contacts.all.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.all.page') }}">
                                <i class="material-icons-outlined">group</i>{{ __('dashboard.sidebar.all_contacts') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.contacts.drivers.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.drivers.page') }}">
                                <i class="material-icons-outlined">drive_eta</i>{{ __('dashboard.sidebar.driver_contacts') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.contacts.users.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.users.page') }}">
                                <i class="material-icons-outlined">person</i>{{ __('dashboard.sidebar.user_contacts') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_tickets')
                <li
                    class="{{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.ticket_categories.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">support_agent</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.support_system') }}</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.tickets.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.tickets.index') }}">
                                <i class="material-icons-outlined">confirmation_number</i>{{ __('dashboard.sidebar.all_tickets') }}
                            </a>
                        </li>
                        @can('view_ticket_categories')
                            <li class="{{ request()->routeIs('admin.ticket_categories.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.ticket_categories.index') }}">
                                    <i class="material-icons-outlined">category</i>{{ __('dashboard.sidebar.categories') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('manage_wallet_codes')
                <li class="{{ request()->routeIs('admin.wallet-codes.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.wallet-codes.index') }}">
                        <div class="parent-icon">
                            <i class="material-icons-outlined">account_balance_wallet</i>
                        </div>
                        <div class="menu-title">{{ __('dashboard.sidebar.wallet_codes') }}</div>
                    </a>
                </li>
            @endcan

            @can('view_coupons')
                <li class="{{ request()->routeIs('admin.coupons.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}">
                        <div class="parent-icon">
                            <i class="material-icons-outlined">local_offer</i>
                        </div>
                        <div class="menu-title">{{ __('dashboard.sidebar.coupons_discounts') }}</div>
                    </a>
                </li>
            @endcan

            @can('view_currencies')
                <li class="{{ request()->routeIs('admin.currencies.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.currencies.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">attach_money</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.currency_management') }}</div>
                    </a>
                </li>
            @endcan

            @can('manage_country_currencies')
                <li class="{{ request()->routeIs('admin.countrycurrencies.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.countrycurrencies.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">currency_exchange</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.country_currencies') }}</div>
                    </a>
                </li>
            @endcan

            @can('view_notifications')
                <li class="{{ request()->routeIs('admin.notfiction.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.notfiction.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">notifications_active</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.notifications') }}</div>
                    </a>
                </li>
            @endcan

            @canany(['view_roles', 'view_permissions', 'manage_user_roles'])
                <li
                    class="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.roles.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">admin_panel_settings</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.access_control') }}</div>
                    </a>
                    <ul>
                        @can('view_roles')
                            <li class="{{ request()->routeIs('admin.roles.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.roles.index') }}">
                                    <i class="material-icons-outlined">security</i>{{ __('dashboard.sidebar.manage_roles') }}
                                </a>
                            </li>
                        @endcan
                        @can('view_permissions')
                            <li class="{{ request()->routeIs('admin.permissions.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.permissions.index') }}">
                                    <i class="material-icons-outlined">vpn_key</i>{{ __('dashboard.sidebar.manage_permissions') }}
                                </a>
                            </li>
                        @endcan
                        @can('manage_user_roles')
                            <li class="{{ request()->routeIs('admin.users.roles.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.users.roles.index') }}">
                                    <i class="material-icons-outlined">group</i>{{ __('dashboard.sidebar.user_roles') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('view_audit_logs')
                <li class="{{ request()->routeIs('admin.audi_logs.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.audi_logs.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">history</i></div>
                        <div class="menu-title">{{ __('dashboard.sidebar.audit_logs') }}</div>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>