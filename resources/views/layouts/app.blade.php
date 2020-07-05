<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ URL::asset('img/site.webmanifest') }}">

    <title>{{ config('app.name') }} - @yield('title')</title>
    <meta property="og:title" content="{{ config('app.name') }} - @yield('title')" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:image" content="{{ URL::asset('img/og-image.png') }})" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="1200" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="@yield('description', 'The Nuclear Project website provides you with a set of tools created by its faction members to aid in the Torn journey')" />

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
