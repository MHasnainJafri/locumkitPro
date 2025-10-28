<header>
    <div class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#gotcms-main-menu">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{route('admin.dashboard.index')}}" class="navbar-brand">
                    <span class="glyphicon glyphicon-flash"></span>
                    Locum kit </a>
            </div>
            <div class="collapse navbar-collapse" id="gotcms-main-menu">
                <ul class="nav navbar-nav">

                    <li>
                        <a href="{{route('admin.dashboard.index')}}" class title="Dashboard">
                            <span class="glyphicon glyphicon-dashboard"></span> Dashboard </a>
                    </li>
                    </li>

                    <li>
                        <a href="{{route('admin.page.index')}}" class="" title="Pages">
                            <span class="glyphicon glyphicon-edit"></span> Pages
                        </a>
                    </li>
                    @cando('user/list')
                    <li>
                        <a href="{{route('admin.users.index')}}" class title="Users">
                            <span class="glyphicon glyphicon-user"></span> User </a>
                    </li>
                    @endcando
                    @cando('job/list')
                    <li>
                        <a href="{{route('admin.jobs.index')}}" class title="Job">
                            <span class="glyphicon glyphicon-briefcase"
                                style="    font-size: 15px;  margin-right: 3px;"></span> Job </a>
                    </li>
                    @endcando

                    @cando('category/list')
                    <li>
                        <a href="{{route('admin.category.index')}}" class title="Configuration">
                            <span class="glyphicon glyphicon-book"></span> Category </a>
                    </li>
                    @endcando
                    @cando('package/list')
                    <li>
                        <a href="{{route('admin.package.index')}}" class title="Configuration">
                            <span class="glyphicon glyphicon-gbp"></span> User Package </a>
                    </li>
                    @endcando

                    @cando('finance/list')
                    <li>
                        <a href="{{route('finance.record')}}" class title="Configuration">
                            <span class="glyphicon glyphicon-gbp"></span> Finance </a>
                    </li>
                    @endcando

                    @cando('feedback/list')
                    <li>
                        <a href="{{route('admin.feedback.index')}}" class title="Configuration">
                            <span class="glyphicon glyphicon-star-empty"></span> User Feedback </a>
                    </li>
                    @endcando

                    @cando('config/system')
                    <li>
                        <a href="{{route('admin.config.index')}}" class title="Configuration">
                            <span class="glyphicon glyphicon-cog"></span> Configuration </a>
                    </li>
                    @endcando

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="javascript:void(0);" onclick="$('#logout-form').submit();" title="Logout" alt="Logout">
                            <span class="glyphicon glyphicon-off"></span> Logout </a>
                    </li>
                    <form style="display: none;" aria-hidden="true" action="/logout" id="logout-form" style="display: inline-block;" method="post" hidden>
                        @csrf
                    </form>
                </ul>
            </div>
        </div>
    </div>
</header>
