@extends('layouts.app')

@section('title', 'Torn Time Tracker')
@section('description')
    A hub for of various important Torn Times displayed in your local time instead of TCT
@endsection

@push('scripts')
    <script type="text/javascript" charset="utf8">
        class TimeCard {
            constructor(id, title, recurring = true, multiplePerDay = false,
                        hour = 0, minute = 0, second = 0, dayOfWeek = null,
                        year = null, month = null, day = null) {
                this.id = id;
                this.title = title;
                this.recurring = recurring;
                this.multiplePerDay = multiplePerDay;
                this.hour = hour;
                this.minute = minute;
                this.second = second;
                this.dayOfWeek = dayOfWeek;
                this.year = year;
                this.month = month;
                this.day = day;
            }
            createNextTime() {
                let day = moment().utc()
                    .hour(this.hour).minute(this.minute).second(this.second);

                // If the event repeats multiple times per day, adjust for that specific case here...
                if (this.multiplePerDay) {
                    if (this.hour === 0)
                        day.hour(moment().utc().hour());
                    else {
                        day.hour(this.hour);
                        while (day.isBefore(moment().utc()))
                            day.add(this.hour, 'h');
                    }
                    if (this.minute === 0)
                        day.minute(moment().utc().minute());
                    else {
                        day.minute(this.minute);
                        while (day.isBefore(moment().utc()))
                            day.add(this.minute, 'm');
                    }
                    if (this.second === 0)
                        day.second(moment().utc().second());
                    else {
                        day.second(this.second);
                        while (day.isBefore(moment().utc()))
                            day.add(this.second, 's');
                    }
                }

                // If day already passed, and recurring, increment day.
                if (this.recurring && day.isBefore(moment().utc()))
                    day.add(1, 'd');

                // If the event is weekly then adjust for weekly specific time.
                if (this.dayOfWeek != null) {
                    day.day(this.dayOfWeek);  // 0-6, 0 = Sunday, 1 = Monday, 6 = Saturday, etc...
                    if (day.isBefore(moment().utc()))
                        day.add(7, 'd');
                }

                // If year, month, day data is given ensure the created time has it.
                if (this.year != null)
                    day.year(this.year);
                if (this.month != null && Number.isInteger(this.month))
                    day.month(this.month - 1);
                else if (this.month != null)
                    day.month(this.month);
                if (this.day != null)
                    day.date(this.day);
                return day;
            }
            get toString() {
                moment.relativeTimeThreshold('h', 30);
                moment.relativeTimeThreshold('m', 60);
                moment.relativeTimeThreshold('s', 60);
                let minutes = "";
                let day = this.createNextTime();
                if (day.minute() !== 0) minutes = ":mm";
                let dateText = day.local().format("ddd [@] h" + minutes + "a");
                if (day.local().isSame(moment(), 'day'))
                    dateText = day.local().format("[Today @] h" + minutes + "a");
                return day.fromNow()
                    + "</br>" + dateText;
            }
        }

        let cards = [
            new TimeCard('company-day', 'Company Day', true, false, 18),
            new TimeCard('torn-day', 'Torn Day', true, false),
            new TimeCard('company-week', 'Company Week', true, false, 18, 0, 0, 0),
            new TimeCard('addiction-decay', 'Addiction Decay', true, false, 5, 30),
            new TimeCard('chain', 'Chaining', false, false, 12, 0, 0, null, 2021, 3, 26),
            new TimeCard('store-stock', 'Torn Store Restock', true, true, 0, 15),
        ];

        $(document).ready( function () {
            // Generate and insert the HTML for the default time cards
            createDefaultCards();
            updateTimes();
            setInterval(updateTimes, 7500); //every 10 seconds update it.
        });

        function updateTimes() {
            // Current time/date
            $("#datetime").text(moment().format("ddd, hh:mma"))

            cards.forEach(function (card) {
                $('#' + card.id).html(card.toString);
            });
        }

        function createDefaultCards() {
            cards.forEach(function (card) {
                createNewCard(card.id, card.title);
            });
        }

        function createNewCard(id, title) {
            let html = "<div class=\"lg:w-1/3 px-3 pb-6\">\n" +
                "<div class=\"bg-white p-5 rounded-lg shadow\">\n" +
                "    <h2 class=\"text-2xl text-center\">"+ title +"</h2>\n" +
                "    <p id=\""+ id +"\" class=\"text-center\"></p>\n" +
                "</div></div>"
            $("#card-holder").append(html);
        }
    </script>
@endpush

{{--@include('widgets.login')--}}

@section('content')
    <h1 class="text-4xl text-center pb-3">Torn Important Times Tracker</h1>

    <main id="card-holder"
          class="mx-auto w-2/3 md:min-w-50 box-border p-4 border-2 lg:flex lg:flex-wrap">
        {{--    Dynamically inserted cards into here    --}}
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
