<!DOCTYPE html>
<html>

<head>
    <base href="{{url('/')}}">
    <meta charset="utf-8" />
    <title>Travels</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- C3 charts css -->
    <link href="plugins/c3/c3.min.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" />
    <script src="assets/js/modernizr.min.js"></script>

</head>


<body>
    <div id="app">
        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <div class="topbar">

                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="index.html" class="logo">
                        <span>
                            <img src="assets/images/logo2.png" alt="" height="25">
                        </span>
                        <i>
                            <img src="assets/images/logo2.png" alt="" height="28">
                        </i>
                    </a>
                </div>

                <nav class="navbar-custom">

                    <ul class="list-inline float-right mb-0">
                        <li class="list-inline-item dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="dripicons-bell noti-icon"></i>
                                <span class="badge badge-pink noti-icon-badge">4</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-lg" aria-labelledby="Preview">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5><span class="badge badge-danger float-right">5</span>Notification</h5>
                                </div>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-success"><i class="icon-bubble"></i></div>
                                    <p class="notify-details">
                                    {{ Auth::guard('admin')->user()->fullname}}
                                    <small class="text-muted">1 min ago</small></p>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-info"><i class="icon-user"></i></div>
                                    <p class="notify-details">New user registered.<small class="text-muted">1 min ago</small></p>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <div class="notify-icon bg-danger"><i class="icon-like"></i></div>
                                    <p class="notify-details">Carlos Crouch liked <b>Admin</b><small class="text-muted">1 min ago</small></p>
                                </a>

                                <!-- All-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item notify-all">
                                    View All
                                </a>

                            </div>
                        </li>

                        <li class="list-inline-item dropdown notification-list">
                            <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="assets/images/users/avatar-1.jpg" alt="user" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="text-overflow"><small>Welcome !  {{ Auth::guard('admin')->user()->fullname }}</small> </h5>
                                </div>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle"></i> <span>Profile</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-settings"></i> <span>Settings</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-lock-open"></i> <span>Lock Screen</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <i class="mdi mdi-power"></i> <span>Logout</span>
                                </a>

                            </div>
                        </li>

                    </ul>

                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left">
                            <button class="button-menu-mobile open-left waves-light waves-effect" onclick="initLeftMenuCollapse()">
                                <i class="dripicons-menu"></i>
                            </button>
                        </li>
                    </ul>

                </nav>

            </div>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="slimscroll-menu" id="remove-scroll">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu" id="side-menu">
                            <li>
                                <router-link :to="{ name:'dashboard' }" tag="a"><i class="fi-help"></i><span> Dashboard </span></router-link>
                            </li>                           
                            <!-- <li class="menu-title">User Managemant</li> -->
                            <li>
                                <a href="javascript: void(0);">
                                    <i class="fa fa-users"></i>
                                    <span> User Management </span><span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded=false>
                                    <router-link :to="{ name:'listUsers' }" tag="li">
                                        <a>Users</a>
                                    </router-link>
                                    <router-link :to="{ name:'listTransports' }" tag="li">
                                        <a>Transports</a>
                                    </router-link>
                                    <router-link :to="{ name:'listCustomers' }" tag="li">
                                        <a>Customers</a>
                                    </router-link>
                                    <router-link :to="{ name:'listDrivers' }" tag="li">
                                        <a>Drivers</a>
                                    </router-link>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">
                                    <i class="fa fa-shield"></i>
                                    <span> Role & Permission</span><span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded=false>
                                    <router-link :to="{ name:'roles' }" tag="li">
                                        <a>Roles</a>
                                    </router-link>
                                    <router-link :to="{ name:'permissions' }" tag="li">
                                        <a>Permissions</a>
                                    </router-link>
                                </ul>
                            </li>
                           
                            <li>
                                <router-link :to="{ name:'Vehicles' }" tag="a"><i class="fa fa-bus"></i> <span> Trucks </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'Enquiries' }" tag="a"><i class="fa fa-file"></i> <span> Enquiries </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'Bookings' }" tag="a"><i class="fa fa-history"></i> <span> Bookings </span></router-link>
                            </li>
                            <li class="menu-title">More</li>


                            <li>
                                <router-link :to="{ name:'Documnets' }" tag="a"><i class="fa fa-upload"></i> <span> Documents </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'TruckTypes' }" tag="a"><i class="fa fa-bus"></i> <span> Truck Types </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'BodyTypes' }" tag="a"><i class="fa fa-truck"></i> <span> Body Types </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'MaterialTypes' }" tag="a"><i class="fa fa-file"></i> <span> Material Types </span></router-link>
                            </li>
                            <li>
                                <router-link :to="{ name:'adminSetting' }" tag="a"><i class="fa fa-cogs"></i> <span> Settings </span></router-link>
                            </li>

                        </ul>

                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <router-view class="ks-column ks-page"></router-view>

                <footer class="footer text-right">
                    2018 - 2019 © LoadToLoad. - cairocorps.com
                </footer>

            </div>


            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->
    </div>




    <!-- jQuery  -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>

    <!-- Counter js  -->
    <script src="plugins/waypoints/jquery.waypoints.min.js"></script>
    <script src="plugins/counterup/jquery.counterup.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="plugins/c3/c3.min.js"></script>

    <!--Echart Chart-->
    <script src="plugins/echart/echarts-all.js"></script>

    <script src="plugins/bootstrap-datetimepicker/moment.min.js"></script>
    <script src="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

    <!-- App js -->
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5GfP-JGYBtymfd6G-Ys6MdSxJYmmsVDU&libraries=places"></script>
    <script src="{{ mix('js/admin/app.js') }}"></script>
   
    <script>
      $(function() {
        $('#side-menu').metisMenu({
            toggle: false
        });
      });
    </script>
</body>

</html>
