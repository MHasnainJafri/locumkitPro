<style>
    .active{
        background-color: #EEEEEE !important;
    }
</style>
<div id="sidebar" class="sidebar sidebar-fixed col-lg-2 visible-lg">
    <ul class="nav nav-list">



        <li id="list_sup" class="{{ request()->routeIs('admin.config.index','email.newsletter.notificationsetting','industry.index') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-cog"></i>
                <span class="menu-text"> Configuration </span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="list_sub">
                @cando('report/blockreport')
                <li>
                    <a href="{{ route('admin.config.index') }}" class="{{ request()->routeIs('blog.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('blog.index') ? 'd-block color' : '' }}"></i>
                        General
                    </a>
                </li>
                <li>
                    <a href="{{ route('email.newsletter') }}" class="{{ request()->routeIs('industry.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-envelope {{ request()->routeIs('industry.index') ? 'd-block color' : '' }}"></i>
                        Email Newsletter
                    </a>
                </li>
                <li>
                    <a href="{{ route('email.newsletter.notificationsetting') }}" class="{{ request()->routeIs('industry.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-envelope {{ request()->routeIs('industry.index') ? 'd-block color' : '' }}"></i>
                        Notification Settings
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        <li id="list_sup" class="{{ request()->routeIs('report.blockuserreport','report.locumPrivatejobReport','report.privatelocumReport', 'report.locumjobReport', 'report.new-user', 'report.leaverUser','report.lastlogin','report.EmployerJobReport','report.locumjobReport') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-list-alt"></i>
                <span class="menu-text"> Report </span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="list_sub">
                @cando('report/blockreport')
                <li>
                    <a href="{{ route('report.blockuserreport') }}" class="{{ request()->routeIs('report.blockuserreport') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.blockuserreport') ? 'd-block color' : '' }}"></i>
                        Block User Report
                    </a>
                </li>
                @endcando
                <li>
                    <a href="{{ route('report.new-user') }}" class="{{ request()->routeIs('report.new-user') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.new-user') ? 'd-block color' : '' }}"></i>
                        New User Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.leaverUser') }}" class="{{ request()->routeIs('report.leaverUser') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.leaverUser') ? 'd-block color' : '' }}"></i>
                        Leave User Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.lastlogin') }}" class="{{ request()->routeIs('report.lastlogin') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.lastlogin') ? 'd-block color' : '' }}"></i>
                        Last Login Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.EmployerJobReport') }}" class="{{ request()->routeIs('report.EmployerJobReport') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.EmployerJobReport') ? 'd-block color' : '' }}"></i>
                        Employer Job Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.locumjobReport') }}" class="{{ request()->routeIs('report.locumjobReport') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.locumjobReport') ? 'd-block color' : '' }}"></i>
                        Locum Job Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.privatelocumReport') }}" class="{{ request()->routeIs('report.privatelocumReport') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.privatelocumReport') ? 'd-block color' : '' }}"></i>
                        Private Locum Report
                    </a>
                </li>
                <li>
                    <a href="{{ route('report.locumPrivatejobReport') }}" class="{{ request()->routeIs('report.locumPrivatejobReport') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('report.locumPrivatejobReport') ? 'd-block color' : '' }}"></i>
                        Locum Private Job Report
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
<div class="col-lg-12 main-content">

    <div id="breadcrumbs" class="breadcrumbs">
        <div id="menu-toggler-container" class="hidden-lg">
            <span id="menu-toggler">
                <i class="glyphicon glyphicon-new-window"></i>
                <span class="menu-toggler-text">Menu</span>
            </span>
        </div>
        <ul class="breadcrumb">
        </ul>
    </div>
</div>