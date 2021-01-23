<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <base href="{{url('/')}}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>Spin & Win - Dragon City Bahrain</title>
    <link rel="shortcut icon" href="img/favicon.png">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css"> <!-- original bootstrap styles -->
    <link rel="stylesheet" href="css/style.css" id="stylesheet"> <!-- universeadmin styles -->
    <link rel="stylesheet" href="css/placeholder-loading.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link id="growl-notification-theme" rel="stylesheet" href="vendor/growl-notification/light-theme.min.css">

    <link rel="stylesheet" href="spinner/main.css" type="text/css" />

    <script src="js/ie.assign.fix.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Shojumaru&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="spin/style.css">


</head>

<body class="js-loading boxed-layout sidebar-sm" style="width:100%;">
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
</body>

<script src="js/jquery-latest.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/growl-notification/growl-notification.min.js"></script>
<script src="vendor/jquery-circle-progress/circle-progress.min.js"></script>
<script src="js/main.js"></script>

<!-- <script type="text/javascript" src="spinner/Winwheel.js"></script>
<script src="spinner/TweenMax.min.js"></script> -->
<script src='spin/TweenMax.min.js'></script>
<script src='spin/Draggable.min.js'></script>
<script src='spin/ThrowPropsPlugin.min.js'></script>
<script src='spin/Spin2WinWheel.js'></script>
<script src='spin/TextPlugin.min.js'></script>

<script src="{{ mix('js/spinner/app.js') }}"></script>

<div class="sidebar-mobile-overlay"></div>

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
    [v-cloak].v-cloak--hidden {
        display: none;
    }

    body {
        background-color: #f1eabe;
    }

    .body-image{
        background-image: url('/image/setting/spin_bg_img');
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }

    .v-select.searchable .dropdown-toggle {
        width: 100% !important;
    }

    .swal2-modal{
        border: 10px solid #cac237 !important;

    }
    .swal2-title {
        color: #f1e9e9 !important;
        font-size: 2.5em !important;
    }

    .swal2-icon.swal2-success .swal2-success-ring {
        border: .25em solid rgb(255, 255, 255) !important;
    }
    body {
        display:flex;
        align-items:center;
    }

    /* #app{
    position: absolute;
	top:0;
	bottom: 0;
	left: 0;
	right: 0;  	
	margin: auto;
    width:100%;
    
}
    @media screen and (max-width: 320px) {
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
}​ 
@media only screen and (max-width: 599px) {
    #app{
        height:60vh;  
    }
}

@media only screen and (min-width: 600px) {
    #app{
        height:50vh;        
    }
}*/

</style>
