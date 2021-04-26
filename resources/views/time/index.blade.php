@extends('layouts.app')

@section('title', 'Torn Time Tracker')
@section('description')
    A hub for of various important Torn Times displayed in your local time instead of TCT
@endsection

@push('scripts')
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
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
            get createNextTime() {
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
                let day = this.createNextTime;
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
            new TimeCard('store-stock', 'Torn Store Restock', true, true, 0, 15),
        ];
        let userCards = [];
        let userImportedCards = @json($times);
        userImportedCards.forEach(function (time) {
            let m = moment.unix(time.event_date_time).utc();
            userCards.push(new TimeCard(time.event_id, time.event_name, time.recurring,
                time.multiple_per_day, m.hour(), m.minute(), m.second(),
                time.day_of_week, m.year(), m.month() + 1, m.date()));
        });

        $(document).ready( function () {
            // Generate and insert the HTML for the default time cards
            createDefaultCards();

            $("#modal-new-countdown").dialog({
                autoOpen: false,
                show: {
                    effect: "fade",
                    duration: 500
                },
                hide: {
                    effect: "fade",
                    duration: 500
                }
            });
            $( "#modal-date" ).datetimepicker({
                dateFormat:'yy-mm-dd',
            });

            $("#new-card-button").click(function() {
                $("#modal-new-countdown").dialog("open");
            });

            $("#save-api-key").click(function () {
               let apiKey = $("#api-key").val();
               $.post("{{ url('/time/api_key') }}", {
                   _token: "{{ csrf_token() }}",
                   api_key: apiKey
               }).done(function (data) {
                   alert(data);
               });
            });

            $("#modal-submit").click(function() {
                let datetime = moment.utc($("#modal-date").val());
                let cardTitle = $("#modal-title").val();
                let cardId = cardTitle.replace(/\s+/g, '-').toLowerCase() + Date.now().toString();
                let newEvent = new TimeCard(cardId, cardTitle, false, false,
                    datetime.hour(), datetime.minute(), 0, null,
                    datetime.year(), datetime.month() + 1, datetime.date());
                userCards.push(newEvent);
                updateTimes();
                $("#modal-new-countdown").dialog("close");

                $.post("{{ url('/time/') }}", {
                    _token: "{{ csrf_token() }}",
                    event_id: cardId,
                    event_name: cardTitle,
                    recurring: newEvent.recurring ? 1 : 0,
                    multiple_per_day: newEvent.multiplePerDay ? 1 : 0,
                    day_of_week: newEvent.dayOfWeek,
                    event_date_time: newEvent.createNextTime.unix()
                }).done(function (data) {
                   alert(data);
                });
            });

            {{--$('#test').click(function() {--}}
            {{--    $.ajax({--}}
            {{--        url:  '{{ url('/time/') }}',--}}
            {{--        type: 'DELETE',--}}
            {{--        data: {--}}
            {{--            _token: '{{ csrf_token() }}'--}}
            {{--        },--}}
            {{--        success: function (result) {--}}
            {{--            alert(result);--}}
            {{--            userCards.forEach(function(card) {--}}
            {{--                let element = $("#" + card.id);--}}
            {{--                element.parents().eq(1).remove();--}}
            {{--            });--}}
            {{--            $("#api-key").val('');--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}

            $("#delete-all-api-data").click(function () {
                $.ajax({
                    url:  '{{ url('/time/destroyAll') }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                       alert(result);
                       userCards.forEach(function(card) {
                           let element = $("#" + card.id);
                           element.parents().eq(1).remove();
                       });
                       $("#api-key").val('');
                       userCards = [];
                    }
                });
            });

            let playerApiKeySession = "{{ session('player.api_key') }}";
            if (playerApiKeySession.length)
                $("#api-key").val("{{ session('player.api_key') }}");

            updateTimes();
            setInterval(updateTimes, 7500); //every 10 seconds update it.
        });

        function updateTimes() {
            // Current time/date
            $("#datetime").text(moment().format("ddd, hh:mma"))
            $("#datetime-tct").text(moment().utc().format("ddd, hh:mma"))

            cards.forEach(function (card) {
                let element = $('#' + card.id);
                if (element.length === 0)
                    createNewCard(card.id, card.title);
                element.html(card.toString);
            });

            userCards.forEach(function (card) {
                let element = $('#' + card.id);
                if (element.length === 0)
                    createNewCard(card.id, card.title);
                element.html(card.toString);
            });
        }

        function createDefaultCards() {
            cards.forEach(function (card) {
                createNewCard(card.id, card.title);
            });
            userCards.forEach(function (card) {
                createNewCard(card.id, card.title);
            });
            createNewCard('new-card', 'Create New Card');
            $("#new-card").html(
                '<button id="new-card-button"' +
                ' class="btn btn-blue-outline">' +
                'New' +
                '</button>')
        }

        function createNewCard(id, title) {
            let html = "<div class=\"lg:w-1/3 px-3 pb-6\">\n" +
                "<div class=\"bg-white p-5 rounded-lg shadow\">\n" +
                "    <h2 class=\"text-2xl text-center\">"+ title +"</h2>\n" +
                "    <p id=\""+ id +"\" class=\"text-center\"></p>\n" +
                "</div></div>"
            let newCard = $("#new-card");
            if (newCard.length)
                newCard.parents().eq(1).before(html);
            else
                $("#card-holder").append(html);
        }
    </script>
@endpush

{{--@include('widgets.login')--}}

@section('content')
    <h1 class="text-4xl text-center pb-1">Torn Important Times Tracker</h1>

    <form class="text-center mb-1" action="#">
{{--        <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="api-key">Torn API key</label>--}}
        <input class="border py-2 px-3 text-grey-darkest" style="text-align: center;" type="text" name="api-key" id="api-key" placeholder="API Key">

        <button id="save-api-key" class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 mt-1 rounded" type="button">Save Key</button>
    </form>

    <main id="card-holder"
          class="mx-auto w-2/3 md:min-w-50 box-border p-4 border-2 lg:flex lg:flex-wrap relative">
        {{--    Dynamically inserted cards into here    --}}
        <span class="absolute bottom-0 right-0 text-purple-400 text-sm pr-1 pb-0.5 italic">
            <a href="#" id="delete-all-api-data">Delete all API and time data</a>
        </span>
    </main>

    <h2 class="text-center text-lg">Local Time: <span class="font-medium" id="datetime"></span></h2>
    <h2 class="text-center text-lg">Torn Time: <span class="font-medium" id="datetime-tct"></span></h2>


    {{--  Modal for Creating New Countdown  --}}
    <div id="modal-new-countdown" class="modal" title="Create New Countdown">
        <div role="dialog">
            <div id="modal-1-content">
                <form class="w-full max-w-sm">
                    <div class="md:flex md:items-center mb-6">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                                Event
                            </label>
                        </div>
                        <div class="md:w-2/3">
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full
                                py-2 px-4 text-gray-700 leading-tight focus:outline-none
                                focus:bg-white focus:border-purple-500" id="modal-title"
                                       type="text" placeholder="Event Name">
                        </div>
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-password">
                                Time
                            </label>
                        </div>
                        <div class="md:w-2/3">
                            <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="modal-date" type="text" placeholder="">
                        </div>
                    </div>
                    <div class="md:flex md:items-center">
                        <div class="md:w-1/3"></div>
                        <div class="md:w-2/3">
                            <button id="modal-submit" class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="button">
                                Add Event
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @php
        //$time_end = microtime(true);

        //$execution_time = ($time_end - $time_start)/60;
        //echo '<b>Total Execution Time:</b> '.number_format((float) $execution_time, 10) .' Minutes<br>';
        //echo "<b>Players Checked: </b>" . $i . "<br>";
        //echo "<b>Last Generated: </b>" . date("Y-m-d H:i") . " EST" . "<br>";
    @endphp
@endsection
