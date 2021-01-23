<!DOCTYPE html>

<head>
    <base href="{{url('/')}}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta name="description" content="">
    <meta name="keywords"  content="">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <script src="js/jquery-latest.min.js"></script> 
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</head>
<style>
    td span{
        display:block;
    }
    td span {
    display: block;
    padding: .125rem 1.5rem;
    color: #99979c;
}
</style>
<body>
    <div class="container-fuild">
        <div id="main" class="well main-content">
            <div class="row-fluid">                
                <div class="table-responsive">
                   @yield('content')                   
                </div>
            </div>
        </div>
    </div>
</body>

</html>