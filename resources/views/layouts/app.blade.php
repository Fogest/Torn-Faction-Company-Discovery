<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Nuclear Company Directory</title>

    <!-- Fonts -->
{{--        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">--}}

<!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            /*font-family: 'Nunito', sans-serif;*/
            /*font-weight: 200;*/
            /*height: 100vh;*/
            /*margin: 0;*/
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

    <!-- Scripts -->

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8">
        $(document).ready( function () {
            $('#directory-table').DataTable({
                "paging": false,
                "order": [[2, 'asc'], [3, 'dsc']],
                "columnDefs": [
                    {
                        targets: [0,1,2,3,4],
                        className: 'dt-body-center'
                    }
                ],
                "rowCallback": function(row, data, index) {
                    let companyName = data[1].replace(/\s+/g, '').toLowerCase(); // Strip whitespace and make lowercase
                    if (companyName.includes('hiring') || companyName.includes('hire')) {
                        $('td', row).css('background-color', '#ffc8008c');
                    }

                    let positions = data[4];
                    let positionsSplit = positions.split('/');
                    if(positionsSplit[0] !== positionsSplit[1]) {
                        $(row).find('td:eq(4)').css('background-color', '#f75e5ead');
                    }
                }
            });

            $('#directory-table-debug').DataTable({
                "paging": false,
            });
        } );
    </script>

</head>
<body>
    @yield('content')
</body>
</html>
