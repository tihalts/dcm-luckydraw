<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <base href="{{url('/')}}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>Dashboard</title>
    <link rel="shortcut icon" href="img/favicon.png">
    <style>
        .page-preloader__logo img {
            height: 200px !important;
        }

    </style>

    <link rel="stylesheet" href="fonts/open-sans/style.min.css"> <!-- common font  styles  -->
    <link rel="stylesheet" href="fonts/universe-admin/style.css"> <!-- universeadmin icon font styles -->
    <link rel="stylesheet" href="fonts/mdi/css/materialdesignicons.min.css"> <!-- meterialdesignicons -->
    <link rel="stylesheet" href="fonts/iconfont/style.css"> <!-- DEPRECATED iconmonstr -->
    <link rel="stylesheet" href="vendor/simplebar/simplebar.css"> <!-- original simplebar plugin (scrollbar) styles  -->
    <link rel="stylesheet" href="vendor/tagify/tagify.css"> <!-- styles for tags -->
    <link rel="stylesheet" href="vendor/tippyjs/tippy.css"> <!-- original tippy plugin (tooltip) styles -->
    <link rel="stylesheet" href="vendor/select2/css/select2.min.css"> <!-- original select2 plugin styles -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"> <!-- original bootstrap styles -->
    <link rel="stylesheet" href="css/style.css" id="stylesheet"> <!-- universeadmin styles -->
    <link rel="stylesheet" href="css/placeholder-loading.min.css">

    <script src="js/ie.assign.fix.min.js"></script>
</head>

<body class="js-loading">
    <!-- add for rounded corners: form-controls-rounded -->



    <div class="page-preloader js-page-preloader">
        <div class="page-preloader__logo">
            <img src="img/icon-black-logo.png" alt="" class="page-preloader__logo-image">
        </div>
        <div class="page-preloader__desc">Dragon City Bahrain</div>
        <div class="page-preloader__loader">
            <div class="page-preloader__loader-heading">System Loading</div>
            <!-- <div class="page-preloader__loader-desc">Widgets update</div> -->
            <div class="progress progress-rounded page-preloader__loader-progress">
                <div id="page-loader-progress-bar" class="progress-bar bg-info" role="progressbar" style="width: 10%"
                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="page-preloader__copyright">Dragon City Bahrain, 2019</div>
    </div>



    <div class="navbar navbar-light navbar-expand-lg">
        <button class="sidebar-toggler" type="button">
            <span class="ua-icon-sidebar-open sidebar-toggler__open"></span>
            <span class="ua-icon-alert-close sidebar-toggler__close"></span>
        </button>
        <span class="navbar-brand">
            <a href="/" style="margin-left:10px;"><img src="img/full-logo-dark.png" alt=""
                    class="navbar-brand__logo"></a>
        </span>
        <span class="navbar-brand-sm">
            <a href="/dashboard"><img src="img/full-logo-dark.png" alt="" class="navbar-brand__logo"></a>
            <span class="ua-icon-menu slide-nav-toggle"></span>
        </span>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse">
            <span class="ua-icon-navbar-open navbar-toggler__open"></span>
            <span class="ua-icon-alert-close navbar-toggler__close"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-collapse">
            <div class="navbar__menu">
                <ul class="navbar-nav">
                </ul>
            </div>
            <div class="dropdown navbar-dropdown">
                <a class="dropdown-toggle navbar-dropdown-toggle navbar-dropdown-toggle__user" data-toggle="dropdown"
                    href="#">
                    <img src="img/user.png" alt="" class="navbar-dropdown-toggle__user-avatar">
                    <span class="navbar-dropdown__user-name">{{ Auth::user()->fullname}}</span>
                </a>
                <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu__user">
                    <div class="navbar-dropdown-user-content">
                        <div class="dropdown-user__avatar"><img src="img/user.png" alt="" /></div>
                        <div class="dropdown-info">
                            <div class="dropdown-info__name">{{ Auth::user()->fullname}}</div>
                            <div class="dropdown-info__job">Administrator</div>
                        </div>
                    </div>
                    <!-- <router-link  :to="{ name:'changePassword' }" class="dropdown-item navbar-dropdown__item" tag="a" >Change Password</router-link> -->
                    <a class="dropdown-item navbar-dropdown__item" href="/change/password">Change Password</a>
                    <a class="dropdown-item navbar-dropdown__item" href="/logout">Sign Out</a>
                </div>
            </div>
        </div>
    </div>




    <div id="app" class="page-wrap">       

        <div class="page-content" style="margin-left:0px;">

            <div class="container-fluid">
                @yield('content')
            </div>
        </div>


    </div>



    <script src="vendor/echarts/echarts.min.js"></script>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/simplebar/simplebar.js"></script>
    <script src="vendor/text-avatar/jquery.textavatar.js"></script>
    <script src="vendor/tippyjs/tippy.all.min.js"></script>
    <script src="vendor/wnumb/wNumb.js"></script>

    <script src="js/main.js"></script>
</body>

</html>