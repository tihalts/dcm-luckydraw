<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="img/favicon.png">
    <title>Change Password - Dragon City Bahrain</title>


    <link rel="stylesheet" href="fonts/open-sans/style.min.css"> <!-- common font  styles  -->
    <link rel="stylesheet" href="fonts/universe-admin/style.css"> <!-- universeadmin icon font styles -->
    <link rel="stylesheet" href="fonts/mdi/css/materialdesignicons.min.css"> <!-- meterialdesignicons -->
    <link rel="stylesheet" href="fonts/iconfont/style.css"> <!-- DEPRECATED iconmonstr -->
    <link rel="stylesheet" href="vendor/flatpickr/flatpickr.min.css">
    <!-- original flatpickr plugin (datepicker) styles -->
    <link rel="stylesheet" href="vendor/simplebar/simplebar.css"> <!-- original simplebar plugin (scrollbar) styles  -->
    <link rel="stylesheet" href="vendor/tagify/tagify.css"> <!-- styles for tags -->
    <link rel="stylesheet" href="vendor/tippyjs/tippy.css"> <!-- original tippy plugin (tooltip) styles -->
    <link rel="stylesheet" href="vendor/select2/css/select2.min.css"> <!-- original select2 plugin styles -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"> <!-- original bootstrap styles -->
    <link rel="stylesheet" href="css/style.min.css" id="stylesheet"> <!-- universeadmin styles -->



    <script src="js/ie.assign.fix.min.js"></script>
</head>

<body class="p-front">


    <div class="navbar navbar-light navbar-expand-lg p-front__navbar">
        <!-- is-dark -->
        <a class="navbar-brand" href="/" style="margin-left:5px;"><img src="img/full-logo-dark.png" alt="" /></a>
        <a class="navbar-brand-sm" href="/"><img src="img/logo-sm.png" alt="" /></a>


    </div>




    <div class="p-front__content">        
        <div class="p-signin">
        @if (session('error_message'))
        <div class="alert alert-danger">
            {{ session('error_message') }}
        </div>
        @endif
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
            <form class="p-signin__form" method="post" action="/reset-password">
                <h2 class="p-signin__form-heading">Update Password</h2>
                {{ csrf_field() }}
                <div class="p-signin__form-content">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="password">New Password</label>
                            <input class="form-control" type="password" name="password" required="" id="password"
                                placeholder="Enter new password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="confirmation_password">Confirmation Password</label>
                            <input class="form-control" type="password" name="password" required=""
                                id="confirmation_password" placeholder="Re-enter confirmation password">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-info btn-block btn-lg p-signin__form-submit">Sign
                            In</button>
                    </div>
                    <div class="p-signin__form-links">
                        <div class="p-signin__form-link">
                            Forgot password? <a href="/forgot-password" class="link-info">Click here</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>


    <footer class="p-front__footer">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="#">Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">FAQ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Terms of Service</a>
            </li>
        </ul>
        <span>2019 &copy; Dragon City Bahrain</span>
    </footer>



    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/js/select2.full.min.js"></script>
    <script src="vendor/simplebar/simplebar.js"></script>
    <script src="vendor/text-avatar/jquery.textavatar.js"></script>
    <script src="vendor/tippyjs/tippy.all.min.js"></script>
    <script src="vendor/flatpickr/flatpickr.min.js"></script>
    <script src="vendor/wnumb/wNumb.js"></script>
    <script src="js/main.js"></script>



    <div class="sidebar-mobile-overlay"></div>

</body>

</html>
