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
            <img alt="Logo" src="{{ asset('assets/media/logos/gas-station.png') }}" class="app-sidebar-logo-minimize"
                style="height: 30px;" />
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
                        <a class="menu-link {{ active_menu('dashboard', null, 'link') }}"
                            href="{{ route('/') }}">
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

                    <!-- My ITM -->
                    <div class="menu-item menu-accordion {{ active_menu(['farmers', 'register-farmer', 'edit-farmer', 'farmer-profiles'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">My ITM</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['farmers', 'register-farmer', 'edit-farmer', 'farmer-profile'], null, 'submenu') }}">

                            <!-- Assigned Assets -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('farmers', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Assigned Assets</span>
                                </a>
                            </div>

                            <!-- Downstream Assets -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('farmer-profiles', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Downstream Assets</span>
                                </a>
                            </div>

                            <!-- Trouble Tickets -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('farmer-profiles', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Trouble Tickets</span>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Assets -->
                    <div class="menu-item menu-accordion {{ active_menu(['assets'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Assets</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion {{ active_menu(['assets'], null, 'menu') }}">

                            <!-- Assign Asset -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('assign-asset', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Assign Asset</span>
                                </a>
                            </div>

                            <!-- Registered Assets -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('all-assets', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Registered Assets</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['inventories'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Stock</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['inventories', 'request-for-stock.index', 'request-for-stock.create'], null, 'menu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('inventories', null, 'link') }}" href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Stock Entry</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Available Stock</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Requisition Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['inventories'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Requisitions</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['inventories', 'request-for-stock.index', 'request-for-stock.create'], null, 'menu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Requisitions</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Purchase Requests</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service & Warranty Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['inventories'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Service & Warranty</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['inventories', 'request-for-stock.index', 'request-for-stock.create'], null, 'menu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Service Claims</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Warranty Claims</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Disposal Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['inventories'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Disposal</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['inventories', 'request-for-stock.index', 'request-for-stock.create'], null, 'menu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Disposed Assets</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Damaged Assets</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Lost / Stolen Assets</span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu(['request-for-stock.index', 'request-for-stock.create'], null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Obsolete Assets </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Lifecycle Actions -->
                    <div class="menu-item menu-accordion {{ active_menu(['lifecycle'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-20 fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Lifecycle Actions</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion {{ active_menu(['lifecycle'], null, 'menu') }}">

                            <!-- Asset Disposal / Decommissioning -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('asset-disposal.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Decommissioning</span>
                                </a>
                            </div>

                            <!-- Lifecycle History -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('lifecycle-history.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Lifecycle History</span>
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

                    <!-- Vendor Management -->
                    <div class="menu-item menu-accordion {{ active_menu(['lifecycle'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-user-tick  fs-1 text-success"></i>
                            </span>
                            <span class="menu-title">Vendor Management</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion {{ active_menu(['lifecycle'], null, 'menu') }}">

                            <!-- Active Users -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('asset-disposal.index', null, 'link') }}"
                                    href="#">
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

                    <!-- REFENCES DATA -->
                    <div class="menu-item menu-accordion {{ active_menu(['lifecycle'], null, 'menu') }}"
                        data-kt-menu-trigger="click">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-data fs-1 text-warning"></i>
                            </span>
                            <span class="menu-title">Reference Data</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion {{ active_menu(['lifecycle'], null, 'menu') }}">

                            <!-- TT Issue Types -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('asset-disposal.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">TT Issue Types</span>
                                </a>
                            </div>

                            <!-- Device Categories -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('lifecycle-history.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Device Categories</span>
                                </a>
                            </div>

                            <!-- Device Models & Brands -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('lifecycle-history.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Device Models & Brands</span>
                                </a>
                            </div>

                            <!-- Technical Specifications -->
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('lifecycle-history.index', null, 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Technical Specifications</span>
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
                                <i class="ki-solid ki-lock-3 fs-1" style="color:#fbff00;"></i>
                                <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Access Management</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div
                            class="menu-sub menu-sub-accordion {{ active_menu(['users.index'], 'users', 'submenu') }}">
                            <div class="menu-item">
                                <a class="menu-link {{ active_menu('users.index', 'users', 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">User</span>
                                </a>
                                <a class="menu-link {{ active_menu('roles.index', 'roles', 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Role</span>
                                </a>
                                <a class="menu-link {{ active_menu('permissions.index', 'permissions', 'link') }}"
                                    href="#">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Permission</span>
                                </a>
                                <a class="menu-link {{ active_menu('permissions.index', 'permissions', 'link') }}"
                                    href="{{ route('user-role.index') }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">User Role</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Cache -->
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('optimize-clear') }}">
                            <span class="menu-icon">
                                <i class="fas fa-broom fs-1" style="color:#ff5722;"></i>
                                <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Clear Cache</span>
                        </a>
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
