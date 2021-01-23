<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta name="description" content="">
    <meta name="keywords"  content="">
    <!-- Latest compiled and minified CSS -->
   
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
                                @if(isset($reports[0]))
                                @foreach(array_keys($reports[0]) as $key)
                                <th>{{ $key }}</th>
                                @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $key => $report)
                            <tr>
                                @foreach($report as $key1 => $value1)
                                @if(is_array($value1))
                                <td>
                                    @foreach($value1 as $value)
                                    <span>{{ $value }}</span>
                                    @endforeach
                                </td>
                                @else
                                <td>{{ $value1 }}</td>
                                @endif
                                @endforeach                        
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