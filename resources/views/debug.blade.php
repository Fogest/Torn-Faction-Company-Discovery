@extends('layouts.app')
@section('content')
    <table id='directory-table-debug' class='display'>
        <thead>
        <tr>
            <th>Player Name</th>
            <th>pid</th>
            <th>p updated</th>
            <th>Company Name</th>
            <th>Type</th>
            <th>Rank</th>
            <th>Positions</th>
            <th>Owner?</th>
            <th>Created</th>
            <th>Updated</th>
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
                <td>{{ $company->player->id }}</td>
                <td title="{{ $company->player->last_complete_update_at }}">
                    {{ Carbon\Carbon::parse($company->player->last_complete_update_at)->diffForHumans() }}</td>
                <td>
                    <a href='https://www.torn.com/joblist.php#/p=corpinfo&ID={{ $company->id }}'>
                        {!! html_entity_decode($company->name) !!}
                    </a>
                </td>
                <td>{{ $company->type->name }}</td>
                <td> {{ $company->rank }}</td>
                <td> {{ $company->hired_employees }}/{{ $company->max_employees }}</td>
                <td>{{ $company->isOwner }}</td>
                <td title="{{ $company->created_at }}">{{ $company->created_at->diffForHumans() }}</td>
                <td title="{{ $company->updated_at }}">{{ $company->updated_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
