<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta name="description" content="">
    <meta name="keywords"  content="">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher['Campaign Name'] }} </td>
                                <td>{{ $voucher['Voucher Code'] }}</td>
                                <td>
                                    {{ $voucher['Customer Name'] }} <br/>
                                    {{ $voucher['Customer CPR'] }}<br/>
                                    {{ $voucher['Customer Email'] }}<br/>
                                    {{ $voucher['Customer mobile'] }}<br/>
                                </td>
                                <td>
                                    <span>{{ $voucher['Provider By'] }}</span>
                                    <span>{{ $voucher['Provider Role'] }}</span>
                                </td>
                                <td>{{ $voucher['Created At'] }}</td>
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