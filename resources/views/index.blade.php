<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
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
            } );
        </script>

    </head>
    <body>
        <table id='directory-table' class='display'>
            <thead>
                <tr>
                    <th>Player Name</th>
                    <th>Company Name</th>
                    <th>Type</th>
                    <th>Rank</th>
                    <th>Positions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($companies as $company)
                    <tr>
                        <td>
                            <a href='https://www.torn.com/profiles.php?XID={{ $company->player->id }}'>
                                {{ $company->player->name }}
                            </a>
                        </td>
                        <td>
                            <a href='https://www.torn.com/joblist.php#/p=corpinfo&userID={{ $company->id }}'>
                                {{ $company->name }}
                            </a>
                        </td>
                        <td>{!! $company->type->name !!}</td>
                        <td> {{ $company->rank }}</td>
                        <td> {{ $company->hired_employees }}/{{ $company->max_employees }}</td>
                    </tr>
                @empty
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @php
            //$time_end = microtime(true);

            //$execution_time = ($time_end - $time_start)/60;
            //echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Minutes<br>';
            //echo "<b>Players Checked: </b>" . $i . "<br>";
            echo "<b>Last Generated: </b>" . date("Y-m-d H:i") . " EST" . "<br>";
        @endphp
        @if (env('APP_ENV') === "local")
            <b><a href='main.php'>Force Generate</a></b><br>;
        @endif
    </body>
</html>
