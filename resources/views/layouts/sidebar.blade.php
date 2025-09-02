<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="logo-icon">
            <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-img" alt="Maxton Logo">
        </div>
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0">Maxton</h5>
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
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>
            @endcan

            @can('view_users')
                <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">people</i></div>
                        <div class="menu-title">User Management</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.users.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index') }}">
                                <i class="material-icons-outlined">arrow_right</i>All Users
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_drivers')
                <li class="{{ request()->routeIs('admin.drivers.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">drive_eta</i></div>
                        <div class="menu-title">Driver Management</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.drivers.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.index') }}">
                                <i class="material-icons-outlined">arrow_right</i>All Drivers
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.unverified.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.unverified.index') }}">
                                <i class="material-icons-outlined">pending</i>Pending Verification
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.available.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.available.index') }}">
                                <i class="material-icons-outlined">check_circle</i>Available Drivers
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.drivers.unavailable.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.drivers.unavailable.index') }}">
                                <i class="material-icons-outlined">cancel</i>Unavailable Drivers
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_trips')
                <li class="{{ request()->routeIs('admin.trips.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">local_taxi</i></div>
                        <div class="menu-title">Trip Management</div>
                    </a>
                    <ul>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'pending' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'pending']) }}">
                                <i class="material-icons-outlined">schedule</i>Pending Trips
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'accepted' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'accepted']) }}">
                                <i class="material-icons-outlined">thumb_up</i>Accepted Trips
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'in_progress' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'in_progress']) }}">
                                <i class="material-icons-outlined">directions</i>In Progress
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'completed' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'completed']) }}">
                                <i class="material-icons-outlined">done_all</i>Completed Trips
                            </a>
                        </li>
                        <li
                            class="{{ request()->routeIs('admin.trips.index') && request('status') == 'cancelled' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.trips.index', ['status' => 'cancelled']) }}">
                                <i class="material-icons-outlined">clear</i>Cancelled Trips
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('view_vehicle_types')
                <li class="{{ request()->routeIs('admin.vehicle_types.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">directions_car</i></div>
                        <div class="menu-title">Vehicle Management</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.vehicle_types.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.vehicle_types.index') }}">
                                <i class="material-icons-outlined">category</i>Vehicle Types
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
                        <div class="menu-title">Location Management</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.countries.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.countries.index') }}">
                                <i class="material-icons-outlined">public</i>Countries
                            </a>
                        </li>
                        @can('view_cities')
                            <li class="{{ request()->routeIs('admin.cities.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.cities.index') }}">
                                    <i class="material-icons-outlined">location_city</i>Cities
                                </a>
                            </li>
                        @endcan
                        @can('view_districts')
                            <li class="{{ request()->routeIs('admin.districts.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.districts.index') }}">
                                    <i class="material-icons-outlined">place</i>Areas
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
                        <div class="menu-title">Contact Management</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.contacts.all.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.all.page') }}">
                                <i class="material-icons-outlined">group</i>All Contacts
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.contacts.drivers.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.drivers.page') }}">
                                <i class="material-icons-outlined">drive_eta</i>Driver Contacts
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.contacts.users.page') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.contacts.users.page') }}">
                                <i class="material-icons-outlined">person</i>User Contacts
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
                        <div class="menu-title">Support System</div>
                    </a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.tickets.index') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.tickets.index') }}">
                                <i class="material-icons-outlined">confirmation_number</i>All Tickets
                            </a>
                        </li>
                        @can('view_ticket_categories')
                            <li class="{{ request()->routeIs('admin.ticket_categories.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.ticket_categories.index') }}">
                                    <i class="material-icons-outlined">category</i>Categories
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
                        <div class="menu-title">Wallet Codes</div>
                    </a>
                </li>
            @endcan

            @can('view_coupons')
                <li class="{{ request()->routeIs('admin.coupons.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}">
                        <div class="parent-icon">
                            <i class="material-icons-outlined">local_offer</i>
                        </div>
                        <div class="menu-title">Coupons & Discounts</div>
                    </a>
                </li>
            @endcan

            @can('view_currencies')
                <li class="{{ request()->routeIs('admin.currencies.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.currencies.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">attach_money</i></div>
                        <div class="menu-title">Currency Management</div>
                    </a>
                </li>
            @endcan

            @can('manage_country_currencies')
                <li class="{{ request()->routeIs('admin.countrycurrencies.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.countrycurrencies.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">currency_exchange</i></div>
                        <div class="menu-title">Country Currencies</div>
                    </a>
                </li>
            @endcan

            @can('view_notifications')
                <li class="{{ request()->routeIs('admin.notfiction.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.notfiction.index') }}">
                        <div class="parent-icon"><i class="material-icons-outlined">notifications_active</i></div>
                        <div class="menu-title">Notifications</div>
                    </a>
                </li>
            @endcan

            @canany(['view_roles', 'view_permissions', 'manage_user_roles'])
                <li
                    class="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.users.roles.*') ? 'mm-active' : '' }}">
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="material-icons-outlined">admin_panel_settings</i></div>
                        <div class="menu-title">Access Control</div>
                    </a>
                    <ul>
                        @can('view_roles')
                            <li class="{{ request()->routeIs('admin.roles.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.roles.index') }}">
                                    <i class="material-icons-outlined">security</i>Manage Roles
                                </a>
                            </li>
                        @endcan
                        @can('view_permissions')
                            <li class="{{ request()->routeIs('admin.permissions.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.permissions.index') }}">
                                    <i class="material-icons-outlined">vpn_key</i>Manage Permissions
                                </a>
                            </li>
                        @endcan
                        @can('manage_user_roles')
                            <li class="{{ request()->routeIs('admin.users.roles.index') ? 'mm-active' : '' }}">
                                <a href="{{ route('admin.users.roles.index') }}">
                                    <i class="material-icons-outlined">group</i>User Roles
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
                        <div class="menu-title">Audit Logs</div>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>
