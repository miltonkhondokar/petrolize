<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="{{ route('/') }}" class="d-flex align-items-center gap-2">
            <!-- Main Logo (Default View) -->
            <img alt="Logo" src="{{ asset('assets/media/logos/gas-station.png') }}" class="app-sidebar-logo-default"
                style="height: 40px;" />

            <!-- App Name -->
            <span class="app-sidebar-logo-default" style="font-size: 1.25rem; color: white; font-weight: 600;">
                {{ config('app.name') }}
            </span>

            <!-- Minimized Logo (When sidebar is collapsed) -->
            <img alt="Logo" src="{{ asset('assets/media/logos/gas-station.png') }}"
                class="app-sidebar-logo-minimize" style="height: 30px;" />
        </a>

        <!-- Sidebar Toggle Button -->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span><span class="path2"></span>
            </i>
        </div>
    </div>
    <!--end::Logo-->

    <!--begin::Sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">

                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">

                    <!-- Dashboard -->
                    <div class="menu-item">
                        <a class="menu-link {{ active_menu('dashboard', null, 'link') }}" href="{{ route('/') }}">
                            <span class="menu-icon">
                                <i class="ki-solid ki-home-3 fs-1 text-primary"></i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>

                    <!-- APPS -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">APPS</span>
                        </div>
                    </div>

                    <!-- Fuel Management -->
                    <div class="menu-item menu-accordion {{ active_menu(
                        [
                            'fuel_purchases.index',
                            'fuel_purchases.create',
                            'fuel_purchases.show',
                            'fuel_sales_days.index',
                            'fuel_sales_days.create',
                            'fuel_sales_days.show',
                            'fuel_sales_days.edit',
                        ],
                        null,
                        'menu',
                    ) }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Fuel</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(
                                [
                                    'fuel_purchases.index',
                                    'fuel_purchases.create',
                                    'fuel_purchases.show',
                                    'fuel_sales_days.index',
                                    'fuel_sales_days.create',
                                    'fuel_sales_days.show',
                                    'fuel_sales_days.edit',
                                ],
                                null,
                                'menu',
                            ) }}">

                            {{-- Purchase --}}
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['fuel_purchases.index', 'fuel_purchases.create', 'fuel_purchases.show'], null, 'link') }}"
                                    href="{{ route('fuel_purchases.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Purchase Entry</span>
                                </a>
                            </div>

                            {{-- Sale --}}
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['fuel_sales_days.index', 'fuel_sales_days.create', 'fuel_sales_days.show', 'fuel_sales_days.edit'], null, 'link') }}"
                                    href="{{ route('fuel_sales_days.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Sale Entry</span>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Vendor Payments Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['vendor_payments.*'], null,'menu',) }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-wallet fs-1 text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Payments</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(
                                ['vendor_payments.index', 'vendor_payments.create', 'vendor_payments.show', 'vendor_payments.edit'],
                                null,
                                'menu',
                            ) }}">
                            {{-- Payment List --}}
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['vendor_payments.index', 'vendor_payments.show'], null, 'link') }}"
                                    href="{{ route('vendor_payments.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Payment List</span>
                                </a>
                            </div>

                            {{-- Create Payment --}}
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['vendor_payments.create'], null, 'link') }}"
                                    href="{{ route('vendor_payments.create') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Add Payment</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['complaints.*'],null,'menu',) }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-danger">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Complaints</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['complaints.*'],null,'menu',) }}">
                            <!-- Fuel Station Complaints -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('complaints.*', null, 'link') }}"
                                    href="{{ route('complaints.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Complaints</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Entry Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['cost-entries.*'],null,'menu',) }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-danger">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Cost Entry</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['cost-entries.*'],null,'menu',) }}">
                            <!-- Fuel Station Complaints -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('cost-entries.*', null, 'link') }}"
                                    href="{{ route('cost-entries.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Cost Entry</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- MASTER DATA -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">MASTER DATA</span>
                        </div>
                    </div>

                    <!-- User Management -->
                    <div class="menu-item">
                        <a class="menu-link {{ active_menu(['user-master-data', 'user-master-data-create'], null, 'link') }}"
                            href="{{ route('user-master-data') }}">
                            <span class="menu-icon">
                                <i class="ki-solid ki-profile-user fs-1 text-primary"></i>
                            </span>
                            <span class="menu-title">User Management</span>
                        </a>
                    </div>

                    <!-- Geo Location Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['regions.*', 'governorates.*', 'cities.*', 'centers.*'], null, 'menu') }}"
                        data-kt-menu-trigger="click">

                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-geolocation fs-1 text-danger"></i>
                            </span>
                            <span class="menu-title">Geo Location</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['regions.*', 'governorates.*', 'cities.*', 'centers.*'], null, 'menu') }}">

                            <!-- Regions -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('regions.*', null, 'link') }}"
                                    href="{{ route('regions.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Regions</span>
                                </a>
                            </div>

                            <!-- Governorates -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('governorates.*', null, 'link') }}"
                                    href="{{ route('governorates.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Governorates</span>
                                </a>
                            </div>

                            <!-- Cities -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('cities.*', null, 'link') }}"
                                    href="{{ route('cities.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Cities</span>
                                </a>
                            </div>

                            <!-- Centers -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('centers.*', null, 'link') }}"
                                    href="{{ route('centers.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Centers</span>
                                </a>
                            </div>

                        </div>
                    </div>



                    <!-- Vendor Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['vendors.*'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-user-tick  fs-1 text-success"></i>
                            </span>
                            <span class="menu-title">Vendor Management</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion {{ active_menu(['vendors.*'], null, 'menu') }}">

                            <!-- Active Users -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['vendors.*'], null, 'link') }}"
                                    href="{{ route('vendors.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Vendors</span>
                                </a>
                            </div>

                            <!-- Device Assignments -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('lifecycle-history.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Performance</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- REFERENCE DATA -->
                    <div class="menu-item menu-accordion {{ active_menu(['fuel.*', 'fuel-station-fuel-type.*', 'fuel-station.*', 'fuel-unit.*', 'fuel-unit-price.*', 'cost-category.*', 'complaint-category.*'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-data fs-1 text-warning"></i>
                            </span>
                            <span class="menu-title">Reference Data</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['fuel.*', 'fuel-station.*', 'fuel-station-fuel-type.*', 'fuel-unit.*', 'fuel-unit-price.*', 'cost-category.*', 'complaint-category.*'], null, 'menu') }}">

                            <!-- Fuel Units -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('fuel-unit.*', null, 'link') }}"
                                    href="{{ route('fuel-unit.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Fuel Units</span>
                                </a>
                            </div>

                            <!-- Fuel Types -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('fuel.*', null, 'link') }}"
                                    href="{{ route('fuel.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Fuel Types</span>
                                </a>
                            </div>

                            <!-- Fuel Stations -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('fuel-station.*', null, 'link') }}"
                                    href="{{ route('fuel-station.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Fuel Stations</span>
                                </a>
                            </div>

                            <!-- Station Wise Fuel Types -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('fuel-station-fuel-type.*', null, 'link') }}"
                                    href="{{ route('fuel-station-fuel-type.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Station Wise Fuel</span>
                                </a>
                            </div>

                            <!-- Station Wise Fuel Prices -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('fuel-unit-price.*', null, 'link') }}"
                                    href="{{ route('fuel-unit-price.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Station Wise Prices</span>
                                </a>
                            </div>

                            <!-- Cost Categories -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('cost-category.*', null, 'link') }}"
                                    href="{{ route('cost-category.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Cost Categories</span>
                                </a>
                            </div>

                            <!-- Complaints Categories -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('complaint-category.*', null, 'link') }}"
                                    href="{{ route('complaint-category.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Complaint Categories</span>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- REPORTS -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">REPORTS</span>
                        </div>
                    </div>

                    <!-- Report Section -->
                    <div class="menu-item menu-accordion {{ active_menu([], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-graph-4 fs-1" style="color:#ff32a3;"></i>
                            </span>
                            <span class="menu-title">Reports</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion {{ active_menu([], null, 'menu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('reports/stock-status', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Stock Status</span>
                                </a>
                            </div>
                            <!-- Vendor & Procurement -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('reports/vendor-performance', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Vendor Performance</span>
                                </a>
                            </div>
                            <!-- Finance & Compliance -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('reports/financials', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Financial & Depreciation</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('reports/audit', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Audit & Compliance</span>
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- System Settings -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">System Settings</span>
                        </div>
                    </div>

                    <!-- Audit -->
                    <div class="menu-item menu-accordion {{ active_menu(['audit.user', 'audit.folder', 'audit.password', 'audit.other'], 'audit.', 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link {{ active_menu('audit.user', 'audit.', 'menu') }}">
                            <span class="menu-icon">
                                <i class="fas fa-shield-alt fs-5" style="color:#ff4081;"></i>
                            </span>
                            <span class="menu-title">Audit</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['audit.user', 'audit.folder', 'audit.password', 'audit.other'], 'audit.', 'submenu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('audit.user', 'audit.user', 'link') }}"
                                    href="{{ route('audit.user.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Activity</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="menu-item menu-accordion {{ active_menu(['users.index', 'roles.index', 'permissions.index'], ['users', 'roles', 'permissions'], 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span
                            class="menu-link {{ active_menu(['users.index', 'roles.index', 'permissions.index'], ['users', 'roles', 'permissions'], 'menu') }}">
                            <span class="menu-icon">
                                <i class="ki-solid ki-lock-3 fs-1" style="color:#fc37ca;"></i>
                            </span>
                            <span class="menu-title">Access Management</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['users.index'], 'users', 'submenu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('users.index', 'users', 'link') }}"
                                    href="{{ route('users.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">User</span>
                                </a>
                                <a class="menu-link {{ active_menu('roles.index', 'roles', 'link') }}"
                                    href="{{ route('roles.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Role</span>
                                </a>
                                <a class="menu-link {{ active_menu('permissions.index', 'permissions', 'link') }}"
                                    href="{{ route('permissions.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Permission</span>
                                </a>
                                <a class="menu-link {{ active_menu(['user-role.index', 'user-role.edit'], 'link') }}"
                                    href="{{ route('user-role.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">User Permission</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section Title -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Help</span>
                        </div>
                    </div>

                    <!-- Documentation -->
                    <div class="menu-item">
                        <a class="menu-link" href="#" target="_blank">
                            <span class="menu-icon">
                                <i class="ki-solid ki-book-square fs-1" style="color: #00bfa5;"></i>
                                <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Documentation</span>
                        </a>
                    </div>
                </div>
                <!--end::Menu-->
            </div>
        </div>
    </div>
    <!--end::Sidebar menu-->
</div>
