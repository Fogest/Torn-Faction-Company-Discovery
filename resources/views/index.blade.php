@extends('layouts.app')

@section('title', 'Company Directory')
@section('description')
A list of all of the companies run by Nuclear Faction family members. Companies with positions open are highlighted.
@endsection

@push('scripts')
    <script type="text/javascript" charset="utf8">
        $(document).ready( function () {
            let directoryTable = $('#directory-table').DataTable({
                "visible": true,
                "api": true,
                "responsive": true,
                "paging": false,
                "order": [[2, 'asc'], [3, 'dsc']],
                "columnDefs": [
                    {
                        targets: [0, 1, 2, 3, 4],
                        className: 'dt-body-center'
                    }
                ],
                "rowCallback": function (row, data, index) {
                    let companyName = data[1].replace(/\s+/g, '').toLowerCase(); // Strip whitespace and make lowercase
                    if (companyName.includes('hiring') || companyName.includes('hire')) {
                        $('td', row).css('background-color', '#ffc8008c');
                    }

                    let positions = data[4];
                    let positionsSplit = positions.split('/');
                    if (positionsSplit[0] !== positionsSplit[1]) {
                        $(row).find('td:eq(4)').css('background-color', '#f75e5ead');
                    }
                }
            })
                .columns.adjust()
                .responsive.recalc();
        });
    </script>
@endpush
@section('content')
<h1 class="">Nuclear Company Directory</h1>
<p class="text-center text-opacity-60 italic">Data automatically updated within 6 hours of changes</p>
<p class="text-center text-opacity-60">Made By <a class="text-yellow-500 text-opacity-77" href="https://www.torn.com/profiles.php?XID=2254826">Fogest [2254826]</a></p>
<table id='directory-table' class='display stripe hover' style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
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
                    <a target="_blank" href='https://www.torn.com/profiles.php?XID={{ $company->player->id }}'>
                        {{ $company->player->name }}
                    </a>
                </td>
                <td>
                    <a target="_blank" href='https://www.torn.com/joblist.php#/p=corpinfo&ID={{ $company->id }}'>
                        {!! html_entity_decode($company->name) !!}
                    </a>
                </td>
                <td>{{ $company->type->name }}</td>
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
    <b><a href='main.php'>Force Generate</a></b><br>
@endif
@endsection
