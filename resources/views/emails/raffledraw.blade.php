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
 
 
table {

  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 50%;
  margin-left: 25%;
	margin-right: 25%;
		 
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  width: 25% ;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.center{
text-align:center;
}

</style>
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
                <div class=""  style="text-align:center;">
                   <img src="{{ asset('img/full-logo-dark.png')}}" alt="" class="widget-user-card__avatar"><br/>
                   <h1 >{{ $lucky_draw->name }}</h1><br/>
                   <div>Campaign period: {{ Carbon\Carbon::parse($lucky_draw->start_at)->format('d F Y') }} to {{ Carbon\Carbon::parse($lucky_draw->end_at)->format('d F Y') }}</div><br/>
                </div>
                <div class="table-responsive winner_selected">
                    <table class="table table-striped">
                        <tbody>                            
                            <tr>
                                <th>Raffle Draw Date :</th+>
                                <td>{{ Carbon\Carbon::parse($winner->updated_at)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Raffle Number :</th>
                                <td>{{ $winner->uuid }}</td>
                            </tr>
                            <tr>
                                <th>Name :</th>
                                <td>{{ $customer->fullname }}</td>
                            </tr>
                            <tr>
                                <th>CPR :</th>
                                <td>{{ $customer->cpr }}</td>
                            </tr>
                            <tr>
                                <th>Mobile :</th>
                                <td>{{ $customer->mobile }}</td>
                            </tr>
                            <tr>
                                <th>Email :</th>
                                <td>{{ $customer->email }}</td>
                            </tr>
                            <tr>
                                <th>Nationality :</th>
                                <td>{{ $customer->nationality->name ?? "" }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
<style>
    .winner_selected th{
    text-align: right;
}
.winner_selected td{
    text-align: left;
}
</style>
</html>