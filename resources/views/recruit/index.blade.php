@extends('layouts.app')

@section('title', 'Recruiting')
@section('description')
    A hub for to see some of the data gathered from the Nuclear Recruiting script.
@endsection

{{--@include('widgets.login')--}}

@section('content')
    <table id='directory-table' class='display'>
        <thead>
        <tr>
            <th>Player</th>
            <th>Faction</th>
            <th>Recruited By</th>
            <th>In Nuke?</th>
            <th>Created</th>
            <th>Updated</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($recruits as $recruit)
            <tr>
                <td>
                    <a href='https://www.torn.com/profiles.php?XID={{ $recruit->player_id }}'>
                        {{ $recruit->player_name }}
                    </a>
                </td>
                <td>
                    <a href='https://www.torn.com/factions.php?step=profile&ID={{ $recruit->faction_id }}'>
                        {!! html_entity_decode($recruit->faction_name) !!}
                    </a>
                </td>
                <td>
                    <a href='https://www.torn.com/profiles.php?XID={{ $recruit->recruited_by_id }}'>
                        {{ $recruit->recruiter->name }}
                    </a>
                </td>
                <td> {{ $recruit->is_accepted }}</td>
                <td title="{{ $recruit->created_at }}">{{ $recruit->created_at->diffForHumans() }}</td>
                <td title="{{ $recruit->updated_at }}">{{ $recruit->updated_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr>
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
