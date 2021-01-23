<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <base href="{{url('/')}}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>Mirror Layout</title>
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

<body class="js-loading boxed-layout sidebar-sm" style="width:100%;">
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

    <div id="app" class="page-wrap">


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
    
    @if($user_role == 'admin')
    <script src="{{ mix('js/admin/app.js') }}"></script>
    @else
    <script src="{{ mix('js/promoter/app.js') }}"></script>
    @endif

    <script src="js/main.js"></script>

    <script src="vendor/growl-notification/growl-notification.min.js"></script>



    <div class="sidebar-mobile-overlay"></div>



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
    [v-cloak].v-cloak--hidden { display:none; }
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

<style>
body { 
    background-image: url('/image/setting/purchase_bg_img');
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
    background-color: #464646;
}
.v-select.searchable .dropdown-toggle {
    width:100% !important;
}
#app{
    position: absolute;
	top:0;
	bottom: 0;
	left: 0;
	right: 0;  	
	margin: auto;
    width:100%;
    
}
/* @media screen and (max-width: 320px) {
    body { 
     background-image: url(img/responsive/xs.jpg);
    }
}
@media (max-width:424px) and (min-width:321px) {
    body { 
     background-image: url(img/responsive/sm.jpg);
    }
}​
@media (max-width:767px) and (min-width:425px) {
    body { 
     background-image: url(img/responsive/sm1.jpg);
    }
}​
@media (max-width:1023px) and (min-width:768px) {
    body { 
     background-image: url(img/responsive/md.jpg);
    }
}​
@media (max-width:1139px) and (min-width:1024px) {
    body { 
     background-image: url(img/responsive/md2.jpg);
    }
}​
@media (max-width:2559px) and (min-width:1140px) {
    body { 
     background-image: url(img/responsive/lg.jpg);
    }
}​
@media screen and (min-width: 2560px) {
    body { 
     background-image: url(img/responsive/xl.jpg);
    }
}​ */
@media only screen and (max-width: 599px) {
    #app{
        height:60vh;  
    }
}

@media only screen and (min-width: 600px) {
    #app{
        height:50vh;        
    }
}
</style>
