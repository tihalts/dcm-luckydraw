<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta name="description" content="">
    <meta name="keywords"  content="">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{url('vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('css/font-awesome.min.css')}}">
    <script src="{{url('js/jquery-latest.min.js')}}"></script> 
    <script src="{{url('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Gift</th>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Created By</th>
                                <th>Scratched At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gifts as $gift)
                            <tr>
                                <td>{{ $gift['Campaign Name'] }} </td>
                                <td>{{ $gift['Gift Name'] }}</td>
                                <td>{{ $gift['Gift Code'] }}</td>
                                <td>
                                    {{ $gift['Customer Name'] }} <br/>
                                    {{ $gift['Customer CPR'] }}<br/>
                                    {{ $gift['Customer Email'] }}<br/>
                                    {{ $gift['Customer mobile'] }}<br/>
                                </td>
                                <td>
                                    <span>{{ $gift['Provider By'] }}</span>
                                    <span>{{ $gift['Provider Role'] }}</span>
                                </td>
                                <td>{{ $gift['Scratched At'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>