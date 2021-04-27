@extends('layouts.app')

@section('title', 'Torn Time Tracker')
@section('description')
    A hub for of various important Torn Times displayed in your local time instead of TCT
@endsection

@push('scripts')
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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

            $("#modal-date").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });

            $("#new-card-button").click(function() {
                if ($("#api-key").val() === "") {
                    alert("Must have an API key saved at the top to create custom events");
                    return;
                }
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
                let dateField = $("#modal-date");
                let cardTitleField = $("#modal-title");
                let timezoneField = $("#modal-timezone");

                let timezone = timezoneField.find(":selected").text();
                let cardTitle = cardTitleField.val();
                let datetime = dateField.val();

                if (timezone.toLowerCase() === "tct")
                    datetime = moment.utc(datetime);
                else {
                    datetime = moment(datetime);
                    datetime.utc();
                }


                if (dateField.val() === "" || cardTitle === "") {
                    alert("Both the title and date must be filled in, neither can be blank");
                    return;
                }
                let cardId = cardTitle.replace(/\s+/g, '-').toLowerCase() + Date.now().toString();
                let newEvent = new TimeCard(cardId, cardTitle, false, false,
                    datetime.hour(), datetime.minute(), 0, null,
                    datetime.year(), datetime.month() + 1, datetime.date());
                userCards.push(newEvent);
                updateTimes();

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
                    $("#modal-new-countdown").dialog("close");
                    dateField.val("");
                    cardTitleField.val("");
                });
            });

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
                    element = createNewCard(card.id, card.title);
                element.html(card.toString);
            });

            userCards.forEach(function (card) {
                let element = $('#' + card.id);
                if (element.length === 0)
                    element = createNewCard(card.id, card.title);
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
            return $('#' + id);
        }
    </script>
@endpush

{{--@include('widgets.login')--}}

@section('content')
    <div>
        <h1 class="text-4xl text-center pb-1">Torn Time Tracker</h1>
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>

    <main id="card-holder"
          class="mx-auto w-2/3 md:min-w-50 box-border p-4 border-2 lg:flex lg:flex-wrap relative">
        {{--    Dynamically inserted cards into here    --}}
    </main>

    <form class="text-center mb-1" action="#">
        {{--        <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="api-key">Torn API key</label>--}}
        <input class="border py-2 px-3 text-grey-darkest" style="text-align: center;" type="text" name="api-key" id="api-key" placeholder="API Key">

        <button id="save-api-key" class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 mt-1 rounded" type="button">Save Key</button>
    </form>
    <h3 class="text-center text-base text-purple-500">All times shown are converted from TCT to your local time automatically</h3>
    <h2 class="text-center text-lg">Local Time: <span class="font-medium" id="datetime"></span></h2>
    <h2 class="text-center text-lg">Torn Time: <span class="font-medium" id="datetime-tct"></span></h2>
{{--    <h3 class="text-center text-purple-400 text-sm pr-1 pb-0.5 italic">--}}
{{--            <a href="#" id="delete-all-api-data">Delete all API and time data</a>--}}
{{--    </h3>--}}


    {{--  Modal for Creating New Countdown  --}}
    <div id="modal-new-countdown" class="modal" title="Create New Countdown">
        <div role="dialog">
            <div id="modal-1-content">
                <form class="w-full max-w-sm">
                    <div class="md:flex md:items-center mb-6">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="modal-timezone">
                                Timezone
                            </label>
                        </div>
                        <div class="md:w-2/3">
                            <select name="modal-timezone" id="modal-timezone"
                                    class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full
                                py-2 px-4 text-gray-700 leading-tight focus:outline-none
                                focus:bg-white focus:border-purple-500">
                                <option value="tct" selected>TCT</option>
                                <option value="local">Local</option>
                            </select>
                        </div>
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="modal-title">
                                Event
                            </label>
                        </div>
                        <div class="md:w-2/3">
                                <input class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full
                                py-2 px-4 text-gray-700 leading-tight focus:outline-none
                                focus:bg-white focus:border-purple-500" name="modal-title" id="modal-title"
                                       type="text" placeholder="Event Name">
                        </div>
                    </div>
                    <div class="md:flex md:items-center mb-6">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="modal-date">
                                Date/Time
                            </label>
                        </div>
                        <div class="md:w-2/3">
                            <input class="bg-gray-200 appearance-none border-2
                            border-gray-200 rounded w-full py-2 px-4 text-gray-700
                             leading-tight focus:outline-none focus:bg-white
                              focus:border-purple-500" id="modal-date" name="modal-date"
                                   type="text" placeholder="">
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
