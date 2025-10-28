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
    .context-menu-list {
        display:none !important;
    }

    /* .sidebar{
        position: relative !important;
    } */
</style>

<div class="main-container container">
    <div id="sidebar" class="sidebar sidebar-fixed col-lg-2 visible-lg">

        <ul class="nav nav-list">

            <li class="">
                <a class="menu-toggle" href="#">
                    <i class="glyphicon glyphicon-edit"></i>
                    <span class="menu-text">Page List</span>
                </a>

                <ul class="submenu">
                    <li>
                        <div id="browser">
                            <ul>
                                <li id="documents" class="folder"><ins class="jstree-icon">&nbsp;</ins><a
                                        href=""><ins
                                            style="background:url(/public/media/icons/folder.gif) no-repeat scroll 0 0;"
                                            class="jstree-icon">&nbsp;</ins>Website</a>
                                    <ul>

                                        @foreach ($filesWithParents['root'] as $file)
                                            @php
                                                if (strpos($file, '.blade.php') !== false) {
                                                    $displayFile = str_replace('.blade.php', '', $file);
                                                } else {
                                                    $displayFile = $file; // Keep the original name if it doesn't have '.blade.php'
                                                }
                                            @endphp
                                            <li id="document_137" class="default">
                                                <a id="137" href="{{ route('pages.edit', ['name' => $file]) }}">
                                                    <ins style="background:url(/public/media/icons/image.png) no-repeat scroll 0 0;" class="jstree-icon">&nbsp;</ins>
                                                    {{ $displayFile }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="open">
                <a href="{{ route('admin.page.create') }}">
                    <i class="glyphicon glyphicon-log-in"></i>
                    <span class="menu-text">Create Page</span>
                </a>
            </li>


            <li class="{{ request()->routeIs('blog.index', 'industry.index') ? 'open active' : '' }}">
                <a class="menu-toggle" href="javascript:void(0)">
                    <i class="glyphicon glyphicon-cog"></i>
                    <span class="menu-text">Blog</span>
                    <span class="caret"></span>
                </a>
                <ul class="submenu">

                    <li>
                        <a href="{{ route('blog.index') }}"
                            class="{{ request()->routeIs('blog.index') ? 'active' : '' }}">
                            <i
                                class="glyphicon glyphicon-arrow-right {{ request()->routeIs('blog.index') ? 'd-block color' : '' }}"></i>
                            Blog post
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('industry.index') }}"
                            class="{{ request()->routeIs('industry.index') ? 'active' : '' }}">
                            <i
                                class="glyphicon glyphicon-arrow-right {{ request()->routeIs('industry.index') ? 'd-block color' : '' }}"></i>
                            Industry News </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
