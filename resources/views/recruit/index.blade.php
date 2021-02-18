@extends('layouts.app')

@section('title', 'Recruiting')
@section('description')
    A hub for to see some of the data gathered from the Nuclear Recruiting script.
@endsection

@push('scripts')
    <script type="text/javascript" charset="utf8">
        $(document).ready( function () {
            $('#recruiter-table').DataTable({
                "paging": true,
                "pageLength": 50,
                "order": [[4, 'dsc']],
                "columnDefs": [
                    {
                        targets: [0, 1, 2, 3, 4, 5],
                        className: 'dt-body-center'
                    }
                ],
                "rowCallback": function (row, data, index) {
                    if (data[3] === "Yes")
                        $('td', row).css('background-color', '#b1ffb1');
                }
            });
        });
    </script>
@endpush

{{--@include('widgets.login')--}}

@section('content')
    <table id='recruiter-table' class='display'>
        <thead>
        <tr>
            <th>Player</th>
            <th>Faction</th>
            <th>Recruiter</th>
            <th>Joined?</th>
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
                <td>
                    @if ($recruit->is_accepted)
                        Yes
                    @endif
                </td>
                <td data-sort="{{ $recruit->created_at->timestamp }}" title="{{ $recruit->created_at }}">
                    {{ $recruit->created_at->diffForHumans() }}
                </td>
                <td data-sort="{{ $recruit->updated_at->timestamp }}" title="{{ $recruit->updated_at }}">
                    {{ $recruit->updated_at->diffForHumans() }}
                </td>
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
        <b><a href='{{ url('/recruit/update') }}'>Force Generate</a></b><br>
    @endif
@endsection
