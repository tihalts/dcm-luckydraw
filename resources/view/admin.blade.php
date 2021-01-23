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
    <!-- <link rel="stylesheet" href="vendor/flatpickr/flatpickr.min.css"> -->
    <!-- original flatpickr plugin (datepicker) styles -->
    <link rel="stylesheet" href="vendor/simplebar/simplebar.css"> <!-- original simplebar plugin (scrollbar) styles  -->
    <link rel="stylesheet" href="vendor/tagify/tagify.css"> <!-- styles for tags -->
    <link rel="stylesheet" href="vendor/tippyjs/tippy.css"> <!-- original tippy plugin (tooltip) styles -->
    <link rel="stylesheet" href="vendor/select2/css/select2.min.css"> <!-- original select2 plugin styles -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"> <!-- original bootstrap styles -->
    <link rel="stylesheet" href="css/style.css" id="stylesheet"> <!-- universeadmin styles -->
    <link id="growl-notification-theme" rel="stylesheet" href="vendor/growl-notification/light-theme.min.css">
    <link rel="stylesheet" href="css/placeholder-loading.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">

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
        <div class="sidebar-section">
            <div class="sidebar-section__scroll">
                <div>
                    <!-- <div style="margin-top: 20px;margin-bottom: 20px;">
                        <img src="img/50.png" alt="sdfsdfsd" class="navbar-brand__logo">
                    </div> -->
                    <!-- <div class="sidebar-section__separator">Main</div> -->
                    <ul class="sidebar-section-nav" style="margin-top: 20px;">
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'dashboard' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon ua-icon-home"></span>
                                <span class="sidebar-section-nav__item-text">Dashboard</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Customers' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-users"></span>
                                <span class="sidebar-section-nav__item-text">Customers</span>
                            </router-link>
                        </li>
                        <!-- <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'createCustomerPurchase' }" tag="a"
                                class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-user-plus"></span>
                                <span class="sidebar-section-nav__item-text">Add Customer</span>
                            </router-link>
                        </li> -->
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Purchases' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Purchases</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'createCustomerPurchase' }" tag="a"
                                class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-cart-plus"></span>
                                <span class="sidebar-section-nav__item-text">Add Purchases</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'PurchaseMirror' }" tag="a"
                                target='_blank'
                                class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-window-maximize"></span>
                                <span class="sidebar-section-nav__item-text">Purchase Mirror</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'SpinnerMirror' }" tag="a"
                                target='_blank'
                                class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-window-maximize"></span>
                                <span class="sidebar-section-nav__item-text">Spinner Mirror</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'RewardPoints' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-list-alt"></span>
                                <span class="sidebar-section-nav__item-text">Vouchers</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'ScratchCards' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-qrcode"></span>
                                <span class="sidebar-section-nav__item-text">Scratch Cards</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'SpinWinners' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-spinner"></span>
                                <span class="sidebar-section-nav__item-text">Spin & Wins</span>
                            </router-link>
                        </li>
                    </ul>

                    <div class="sidebar-section__separator">User Management</div>

                    <ul class="sidebar-section-nav">
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'AdminUsers' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-user-secret"></span>
                                <span class="sidebar-section-nav__item-text">Admin Users</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Promoters' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-users"></span>
                                <span class="sidebar-section-nav__item-text">Promoters</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Supervisors' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-users"></span>
                                <span class="sidebar-section-nav__item-text">Supervisors</span>
                            </router-link>
                        </li>
                        <!-- <li class="sidebar-section-nav__item">
                            <a class="sidebar-section-nav__link sidebar-section-nav__link-dropdown" href="#">
                                <span class="sidebar-section-nav__item-icon iconfont-layout"></span>
                                <span class="sidebar-section-nav__item-text">Roles & Permissions</span>
                            </a>
                            <ul class="sidebar-section-subnav" style="display: none;">
                                <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'Roles' }" tag="a" class="sidebar-section-nav__link">
                                        Roles
                                    </router-link>
                                </li>
                                <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'Permissions' }" tag="a" class="sidebar-section-nav__link">
                                      Permissions
                                    </router-link>
                                </li>
                            </ul>
                        </li> -->
                    </ul>

                    <div class="sidebar-section__separator">Settings</div>

                    <ul class="sidebar-section-nav">
                        <!-- <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'PurchasePoints' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon ua-icon-users"></span>
                                <span class="sidebar-section-nav__item-text">Purchese Setting</span>
                            </router-link>
                        </li> 
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Users' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon ua-icon-users"></span>
                                <span class="sidebar-section-nav__item-text">User Settings</span>
                            </router-link>
                        </li> -->
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'BusinessTypes' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-list"></span>
                                <span class="sidebar-section-nav__item-text">Business Types</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Shops' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-shopping-bag"></span>
                                <span class="sidebar-section-nav__item-text">Shops</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <a class="sidebar-section-nav__link sidebar-section-nav__link-dropdown" href="#">
                                <span class="sidebar-section-nav__item-icon fa fa-cog"></span>
                                <span class="sidebar-section-nav__item-text">Campaigns</span>
                            </a>
                            <ul class="sidebar-section-subnav" style="display: none;">
                                <!-- <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'RewardPointCampaigns' }" tag="a" class="sidebar-section-nav__link">
                                    Vouchers
                                    </router-link>
                                </li> -->

                                <li class="sidebar-section-nav__item">
                                    <router-link :to="{ name:'RaffleDraws' }" tag="a" class="sidebar-section-nav__link">
                                      Raffle Draws
                                    </router-link>
                                </li>
                                <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'ScratchCardCampaigns' }" tag="a"
                                        class="sidebar-section-nav__link">
                                        Scratch Cards
                                    </router-link>
                                </li>
                                <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'RewardPointCampaigns' }" tag="a"
                                        class="sidebar-section-nav__link">
                                        Vouchers
                                    </router-link>
                                </li>
                                <li class="sidebar-section-subnav__item">
                                    <router-link :to="{ name:'SpinAndWins' }" tag="a"
                                        class="sidebar-section-nav__link">
                                        Spin & Wins
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'Settings' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-cogs"></span>
                                <span class="sidebar-section-nav__item-text">Settings</span>
                            </router-link>
                        </li>
                        <!-- <li class="sidebar-section-nav__item">
                            <a href="{{ url('/activity') }}" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Activity</span>
                            </a>
                        </li> -->
                    </ul>

                    <div class="sidebar-section__separator">Charts</div>

                    <ul class="sidebar-section-nav">
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'PurchaseCharts' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-bar-chart"></span>
                                <span class="sidebar-section-nav__item-text">Purchases</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'CustomerCharts' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-bar-chart"></span>
                                <span class="sidebar-section-nav__item-text">Customers</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'GiftCharts' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-bar-chart"></span>
                                <span class="sidebar-section-nav__item-text">Gifts</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'VoucherCharts' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-bar-chart"></span>
                                <span class="sidebar-section-nav__item-text">Vouchers</span>
                            </router-link>
                        </li>
                    </ul>

                    <div class="sidebar-section__separator">Reports</div>
                 
                    <ul class="sidebar-section-nav">
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'PurchaseReport' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Purchases</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'ReportPurchaseByCustomers' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Purchase By Customers</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'ReportPurchaseByAmounts' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Purchase By Amount</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'VoucherReport' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Vouchers</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'ScratchCardReport' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Scratch Cards</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'GiftReport' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Gifts</span>
                            </router-link>
                        </li>
                        <li class="sidebar-section-nav__item">
                            <router-link :to="{ name:'ActivityLogs' }" tag="a" class="sidebar-section-nav__link">
                                <span class="sidebar-section-nav__item-icon fa fa-history"></span>
                                <span class="sidebar-section-nav__item-text">Activities</span>
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>



        <router-view></router-view>
    </div>



    <script src="vendor/echarts/echarts.min.js"></script>

    <script src="js/jquery-latest.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/simplebar/simplebar.js"></script>
    <script src="vendor/text-avatar/jquery.textavatar.js"></script>
    <script src="vendor/tippyjs/tippy.all.min.js"></script>
    <!-- <script src="vendor/flatpickr/flatpickr.min.js"></script> -->
    <script src="vendor/wnumb/wNumb.js"></script>
    <script src="vendor/jquery-circle-progress/circle-progress.min.js"></script>

    <script type="text/javascript" src="js/wScratchPad.min.js"></script>
    <script src="{{ mix('js/admin/app.js') }}"></script>

    <script src="js/main.js"></script>

    <script src="vendor/growl-notification/growl-notification.min.js"></script>
</body>

</html>
<script>
    function hidebtnLoader() {
        return;
    }

    function notification(title, message, type = 'warning') {
        GrowlNotification.notify({
            title: title ? title : 'Sorry!',
            description: message ? message : 'Something went worng.!',
            type: type,
            position: 'top-right',
            closeTimeout: 6000
        });
    }

</script>

<style>
    .v-select.searchable .dropdown-toggle {
        width: 100% !important;
    }

    .btn.btn-sm {
        font-size: 19px;
        padding: 5px 8px;
        margin-left: 5px;
    }

    .date-rage-picker.form-control.flatpickr-input.input {
        background: white !important;
    }

</style>
