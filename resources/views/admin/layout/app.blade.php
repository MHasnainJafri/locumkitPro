<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content>
    <title>Admin panel - Locumkit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/less" href="/backend/css/gotcms.less" />
    <script type="text/javascript">
        var less = less || {};
        less.env = "development";
    </script>
    <script src="/backend/js/vendor/less-1.7.0.min.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/backend/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/jquery.browser.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/lib/codemirror.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/mode/xml/xml.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/mode/javascript/javascript.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/mode/css/css.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/mode/clike/clike.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/codemirror/mode/php/php.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/jquery.jstree.js"></script>
    <script type="text/javascript" src="/backend/js/vendor/jquery.contextMenu.js"></script>
    <script type="text/javascript" src="/backend/js/generic-classes.js"></script>
    <script type="text/javascript" src="/admin/translator.js"></script>
    <script type="text/javascript" src="/backend/js/gotcms.js"></script>
</head>

<body id="module-gcbackend">
  @include('admin.layout.header')
   @yield('content')
    <script type="text/javascript">
        Gc.keepAlive('/admin/keep-alive');
    </script>
    <a class="btn-scroll-up btn btn-small btn-inverse" id="btn-scroll-up" href="#">
        <i class="glyphicon glyphicon-open"></i>
    </a>
</body>

</html>
