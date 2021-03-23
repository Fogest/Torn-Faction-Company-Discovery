@extends('layouts.app')

@section('title', 'Recruiting')
@section('description')
    A hub for to see some of the data gathered from the Nuclear Recruiting script.
@endsection

@push('scripts')
    <script type="text/javascript" charset="utf8">
        $(document).ready( function () {
            // Update all the times
            updateTimes();
            setInterval(updateTimes, 10000); //every 10 seconds update it.
        });

        function updateTimes() {
            // Current time/date
            $("#datetime").text(moment().format("ddd, hh:mma"))

            // New Company Day
            $("#new-company-day").html(dateTimeToString(createNextTimeInDay(18)));

            // New Torn Day
            $("#new-torn-day").html(dateTimeToString(createNextTimeInDay()));

            // New Company Week Date
            $("#new-company-week").html(dateTimeToString(createNextDayInWeek(0, 18)));

            // Addiction Decay
            $("#addiction-decay").html(dateTimeToString(createNextTimeInDay(5, 30)));
        }

        function createNextTimeInDay(hour = 0, minute = 0, second = 0) {
            let day = moment().utc().hour(hour).minute(minute).second(second);
            if (day.isBefore(moment().utc()))
                day.add(1, 'd');
            return day;
        }

        function createNextDayInWeek(dayOfWeek = 0, hour = 0, minute = 0, second = 0) {
            let day = createNextTimeInDay(hour, minute, second);
            day.day(dayOfWeek);  // 0-6, 0 = Sunday, 1 = Monday, 6 = Saturday, etc...
            if (day.isBefore(moment().utc()))
                day.add(7, 'd');
            return day;
        }

        function dateTimeToString(momentTimeDate = moment().utc()) {
            moment.relativeTimeThreshold('h', 30);
            moment.relativeTimeThreshold('m', 60);
            moment.relativeTimeThreshold('s', 60);
            let dateText = momentTimeDate.local().format("ddd [@] ha");
            if (momentTimeDate.local().isSame(moment(), 'day'))
                dateText = momentTimeDate.local().format("[Today @] ha");
            return momentTimeDate.fromNow()
                + "</br>" + dateText;
        }
    </script>
@endpush

{{--@include('widgets.login')--}}

@section('content')
    <h1 class="text-4xl text-center pb-3">Torn Important Times Tracker</h1>

    <main class="mx-auto w-2/3 md:min-w-50 box-border p-4 border-2 lg:flex lg:flex-wrap">
        <div class="lg:w-1/3 px-3 pb-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h2 class="text-2xl text-center">New Company Day</h2>
                <p id="new-company-day" class="text-center"></p>
            </div>
        </div>

        <div class="lg:w-1/3 px-3 pb-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h2 class="text-2xl text-center">New Torn Day</h2>
                <p id="new-torn-day" class="text-center"></p>
            </div>
        </div>

        <div class="lg:w-1/3 px-3 pb-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h2 class="text-2xl text-center">New Company Week</h2>
                <p id="new-company-week" class="text-center"></p>
            </div>
        </div>

        <div class="lg:w-1/3 px-3 pb-6">
            <div class="bg-white p-3 rounded-lg shadow">
                <h2 class="text-2xl text-center">Natural Addiction Decay</h2>
                <p id="addiction-decay" class="text-center"></p>
            </div>
        </div>
    </main>
    <h2 class="text-center text-lg">Current local time: <span class="font-medium" id="datetime"></span></h2>

    @php
        //$time_end = microtime(true);

        //$execution_time = ($time_end - $time_start)/60;
        //echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Minutes<br>';
        //echo "<b>Players Checked: </b>" . $i . "<br>";
        //echo "<b>Last Generated: </b>" . date("Y-m-d H:i") . " EST" . "<br>";
    @endphp
@endsection
