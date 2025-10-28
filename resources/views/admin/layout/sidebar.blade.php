<style>
        .custom-scrollbar {
            max-height: 100%; /* Adjust the height as needed */
            overflow-y: auto;
            border: 1px solid #ccc; /* Optional border */
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 12px; /* Width of the entire scrollbar */
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; /* Color of the track */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #888; /* Color of the scroll thumb */
            border-radius: 10px; /* Rounded corners of the scroll thumb */
            border: 3px solid #f1f1f1; /* Padding around the scroll thumb */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
<div id="sidebar" class="sidebar col-lg-2 visible-lg custom-scrollbar">

    <style>
        .glyphicon-arrow-right:before {
            content: "\e092";
            font-family: 'Glyphicons Halflings';
            font-size: 14px;
        }

        .nav-list .menu-toggle.active .glyphicon-arrow-right:before {
            color: #ff0000;
        }

        .d-block {
            display: block !important;
        }

        .color {
            color: #E8B10D !important;
        }

        /* .sidebar{
        position: relative !important;
    } */
    </style>

    <ul class="nav nav-list">
        @cando('user/list')
        <li class="{{ Request::is('admin/users*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-user"></i>
                <span class="menu-text">Users</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="users_sub">
                <li>
                    <a href="{{ route('admin.users.index') }}" class="{{ Request::is('admin/users*') && !Request::is('admin/users/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/users*') && !Request::is('admin/users/create') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                <!--@cando('user/create')-->
                <!--<li>-->
                <!--    <a href="{{ route('admin.users.create') }}" class="{{ Request::is('admin/users*') && !Request::is('admin/users/create') ? 'active' : '' }}">-->
                <!--        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/users*') && !Request::is('admin/users') ? 'd-block color' : '' }}"></i>-->

                <!--        Create-->
                <!--    </a>-->
                <!--</li>-->
                <!--@endcando-->
            </ul>
        </li>
        @endcando

        @cando('role/list')
        <li class="{{ Request::is('admin/roles*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-tower"></i>
                <span class="menu-text">Roles</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="roles_sub">
                <li>
                    <a href="{{ route('admin.roles.index') }}" class="{{ Request::is('admin/roles*') && !Request::is('admin/roles/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/roles*') && !Request::is('admin/roles/create') ? 'd-block color' : '' }}"></i>

                        List
                    </a>
                </li>
                @cando('role/create')
                <li>
                    <a href="{{ route('admin.roles.create') }}" class="{{ Request::is('admin/roles/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/roles*') && !Request::is('admin/roles') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('package/list')
        <li id="package_sup" class="{{ Request::is('admin/package*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-gbp"></i>
                <span class="menu-text">Package</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="package_sub">
                <li>
                    <a href="{{route('admin.package.index')}}" class="{{ Request::is('admin/package*') && !Request::is('admin/package/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/package*') && !Request::is('admin/package/create') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                @cando('package/create')
                <li>
                    <a href="{{route('admin.package.create')}}" class="{{ Request::is('admin/package/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/package/create') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('packageResource/list')
        <li id="package_res_sup" class="{{ Request::is('admin/pacakgeResource*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-flash"></i>
                <span class="menu-text">Package Resources</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="package_res_sub">
                <li>
                    <a href="{{ route('admin.pkgresource.index') }}" class="{{ Request::is('admin/pacakgeResource*') && !Request::is('admin/pacakgeResource/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/pacakgeResource*') && !Request::is('admin/pacakgeResource/create') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                @cando('packageResource/create')
                <li>
                    <a href="{{ route('admin.pkgresource.create') }}" class="{{ Request::is('admin/pacakgeResource/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/pacakgeResource/create') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('category/list')
        <li id="category_sup" class="{{ Request::is('admin/category*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-book"></i>
                <span class="menu-text">Category</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="category_sub">
                <li>
                    <a href="{{ route('admin.category.index') }}" class="{{ Request::is('admin/category*') && !Request::is('admin/category/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/category*') && !Request::is('admin/category/create') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                @cando('category/create')
                <li>
                    <a href="{{ route('admin.category.create') }}" class="{{ Request::is('admin/category/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/category/create') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('question/list')
        <li id="question_sup" class="{{ Request::is('admin/question*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-question-sign"></i>
                <span class="menu-text">Question</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="question_sub">
                <li>
                    <a href="{{ route('admin.question.index') }}" class="{{ Request::is('admin/question*') && !Request::is('admin/question/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/question*') && !Request::is('admin/question/create') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                @cando('question/create')
                <li>
                    <a href="{{ route('admin.question.create') }}" class="{{ Request::is('admin/question/create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ Request::is('admin/question/create') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando


        @cando('paymentHistory/list')
        <li id="paymenthistory_sup" class="{{ request()->routeIs('payment-history') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-bookmark"></i>
                <span class="menu-text">Payment</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="paymenthistory_sub">
                @cando('paymentHistory')
                <li>
                    <a href="{{ route('payment.History') }}" class="{{ request()->routeIs('payment.History') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('payment.History') ? 'd-block color' : '' }}"></i>
                        History
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('feedback/list')
        <li id="feedback_sup" class="{{ request()->routeIs('admin.feedbackquestion*') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-question-sign"></i>
                <span class="menu-text">Feedback Question</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="feedback_sub">
                <li>
                    <a href="{{ route('admin.feedbackquestion.index') }}" class="{{ request()->routeIs('admin.feedbackquestion.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('admin.feedbackquestion.index') ? 'd-block color' : '' }}"></i>
                        List
                    </a>
                </li>
                @cando('feedback/create')
                <li>
                    <a href="{{ route('admin.feedbackquestion.create') }}" class="{{ request()->routeIs('admin.feedbackquestion.create') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('admin.feedbackquestion.create') ? 'd-block color' : '' }}"></i>
                        Create
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('feedback/feedbackList')
        <li id="feedback_list_sup" class="{{ request()->routeIs('admin.userfeedback.index','disputefeedback.list') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-star-empty"></i>
                <span class="menu-text">Feedback Management</span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="feedback_list_sub">
                <li>
                    <a href="{{ route('admin.feedback.index') }}" class="{{ request()->routeIs('admin.feedback.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('admin.feedback.index') ? 'd-block color' : '' }}"></i>
                        User Feedback
                    </a>
                </li>
                @cando('feedback/feedbackDispute')
                <li>
                    <a href="{{ route('disputefeedback.list') }}" class="{{ request()->routeIs('disputefeedback.list') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('disputefeedback.list') ? 'd-block color' : '' }}"></i>
                        Dispute Feedback
                    </a>
                </li>
                @endcando
            </ul>
        </li>
        @endcando

        @cando('finance/list')
        <li class="{{ request()->routeIs('finance.record','tax.list','nitax.list') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-gbp"></i>
                <span class="menu-text">Finance </span>
                <span class="caret"></span>
            </a>
            <ul class="submenu">
                @cando('finance/balancesheet')
                <li>
                    <a href="{{route('finance.record')}}" class="{{ request()->routeIs('finance.record') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('finance.record') ? 'd-block color' : '' }}"></i>
                        Records 
                    </a>
                </li>
                @endcando
                <li>
                    <a href="{{ route('tax.list') }}" class="{{ request()->routeIs('tax.list') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('tax.list') ? 'd-block color' : '' }}"></i>
                        Tax Setting
                    </a>
                </li>
                <li>
                    <a href="{{ route('nitax.list') }}" class="{{ request()->routeIs('nitax.list') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('nitax.list') ? 'd-block color' : '' }}"></i>
                        NI Tax Setting
                    </a>
                </li>
            </ul>
        </li>
        @endcando
        @cando('report/list')
        <!-- <li id="list_sup" class="{{ request()->routeIs('report.blockuserreport','report.locumPrivatejobReport','report.privatelocumReport', 'report.locumjobReport', 'report.new-user', 'report.leaverUser','report.lastlogin','report.EmployerJobReport','report.locumjobReport') ? 'open active' : '' }}">
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
        </li> -->
        @endcando
        @cando('Blog/list')
        <!-- <li id="list_sup" class="{{ request()->routeIs('blog.index','industry.index') ? 'open active' : '' }}">
            <a class="menu-toggle" href="#">
                <i class="glyphicon glyphicon-cog"></i>
                <span class="menu-text"> Blog </span>
                <span class="caret"></span>
            </a>
            <ul class="submenu" id="list_sub">
                @cando('report/blockreport')
                <li>
                    <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('blog.index') ? 'd-block color' : '' }}"></i>
                        Blog post
                    </a>
                </li>
                <li>
                    <a href="{{ route('industry.index') }}" class="{{ request()->routeIs('industry.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-arrow-right {{ request()->routeIs('industry.index') ? 'd-block color' : '' }}"></i>
                        Industry News
                    </a>
                </li> 
                @endcando
            </ul>
        </li> -->
        @endcando
        @cando('Confuguration/list')
        <!-- <li id="list_sup" class="{{ request()->routeIs('admin.config.index','industry.index') ? 'open active' : '' }}">
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
                    <a href="{{ route('industry.index') }}" class="{{ request()->routeIs('industry.index') ? 'active' : '' }}">
                        <i class="glyphicon glyphicon-envelope {{ request()->routeIs('industry.index') ? 'd-block color' : '' }}"></i>
                        Notification Settings
                    </a>
                </li> 
                @endcando
            </ul>
        </li> -->
        @endcando
    </ul>
</div>