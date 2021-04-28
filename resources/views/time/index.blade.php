@extends('layouts.app')

@section('title', 'Torn Time Tracker')
@section('description')
    A hub for of various important Torn Times displayed in your local time instead of TCT
@endsection

@push('scripts')
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/3.4.0/introjs.min.css">
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

            /* Start Intro JS - Site Tour*/
            // check localStorage to see if we've run this before.  If we have then do nothing
            if (localStorage.getItem("first-run") !== 'true') {

                // set a flag in localStorage so we know we've run this before.
                localStorage.setItem("first-run", 'true');

                introJs().start();
            }
            /* End Intro JS */

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

            $("#modal-settings").dialog({
                autoOpen: false,
                height: '350',
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

            $("#settings-button").click(function () {
                $("#modal-settings").dialog("open");
            });

            $("#show-tutorial-again").click(function() {
                localStorage.setItem("first-run", 'false');
                window.location.reload(false);
            });

            $("main").on("click", '.delete-event-icon', function() {
                let eventId = $(this).data("event-id");
                if (!eventId) {
                    alert('Oops, something went wrong. Contact dev');
                    return;
                }
                $.ajax("{{ url('/time/') }}", {
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                        event_id: eventId,
                    },
                    success: function (result) {
                        console.log(userCards);
                        // Filter out card with deleting ID (ie: delete the card from the array)
                        for (let i = userCards.length - 1; i >= 0; --i) {
                            if (userCards[i].id === eventId) {
                                userCards.splice(i,1);
                            }
                        }
                        deleteCard(eventId);
                        console.log(userCards);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown + ': ' + textStatus);
                    }
                });
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
                }).fail(function (data) {
                    alert(data);
                });
                $.get("{{ url('/time/get_times') }}", {
                    _token: "{{ csrf_token() }}",
                    api_key: apiKey
                }).done(function (data) {
                    // No data found, we done here.
                    if (!Object.keys(data).length) return;

                    // Must be data in the JSON array, let's grab the items and display the custom events.
                    userCards.length = 0;
                    data.forEach(function (time) {
                        let m = moment.unix(time.event_date_time).utc();
                        userCards.push(new TimeCard(time.event_id, time.event_name, time.recurring,
                            time.multiple_per_day, m.hour(), m.minute(), m.second(),
                            time.day_of_week, m.year(), m.month() + 1, m.date()));
                    });
                    updateTimes();
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
                    $("#modal-new-countdown").dialog("close");
                    dateField.val("");
                    cardTitleField.val("");
                }).fail(function (data) {
                    alert("Error: " + data);
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
                            deleteCard(card.id);
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

        function deleteCard(id) {
            let element = $("#" + id);
            element.parent().effect("highlight", {color: 'rgb(252, 165, 165)'} , 1000, function() {
                element.parents().eq(1).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }

        function createDefaultCards() {
            cards.forEach(function (card) {
                createNewCard(card.id, card.title, false);
            });
            userCards.forEach(function (card) {
                createNewCard(card.id, card.title);
            });
            createNewCard('new-card', 'Create New Card', false);
            $("#new-card").html(
                '<button id="new-card-button"' +
                ' class="btn btn-blue-outline"' +
                ' data-intro="And once you\'ve added an API key you can add custom events to track in TCT or local times"' +
                ' data-step=4>' +
                'New' +
                '</button>')
        }

        function createNewCard(id, title, custom = true) {
            let firstRunIntroSpew = '';
            if (typeof createNewCard.isFirstRun == 'undefined') {
                createNewCard.isFirstRun = true;
                firstRunIntroSpew = ' data-intro=' +
                    '"Once you add some custom events you can hover over a &quot;card&quot; and click the ❌ icon that will appear (just tap the card instead of hovering on mobile 😊)"' +
                    ' data-step=5 ';
            }

            let customEventContent = '';
            if (custom) {
                customEventContent = '<svg' +
                    ' role=button' +
                    ' data-event-id="'+ id +'"' +
                    ' class="delete-event-icon"' +
                    ' xmlns="http://www.w3.org/2000/svg"' +
                    ' fill="none" viewBox="0 0 24 24"' +
                    ' stroke="currentColor"' +
                    '>\n' +
                    '  <path' +
                    ' stroke-linecap="round"' +
                    ' stroke-linejoin="round"' +
                    ' stroke-width="2"' +
                    ' d="M6 18L18 6M6 6l12 12" />\n' +
                    '</svg>';
            }
            let html = "<div class=\"card-box\"" + firstRunIntroSpew + ">\n" +
                "<div class=\"inner-card-box\">\n" +
                "    <h2 class=\"card-title\">"+ title +"</h2>\n" +
                "    <p id=\""+ id +"\" class=\"card-text\"></p>\n" +
                customEventContent +
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
    <div id="header-title" class="pb-2">
        <h1 class="ttt-title"
            data-title="Welcome!"
            data-intro="Hello, welcome to Torn Time Tracker 👋"
            data-step=1>
            Torn Time Tracker
        <svg
            role="button"
            id="settings-button"
            xmlns="http://www.w3.org/2000/svg"
            class="h-9 w-9 relative inline"
            data-intro="You can adjust your settings by clicking this cog wheel (like your API key)"
            data-step=3
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
        </h1>
    </div>

    <main id="card-holder"
          class="mx-auto w-2/3 md:min-w-50 box-border p-4 mt-1 mb-2 border-2 lg:flex lg:flex-wrap relative"
          data-intro="All the times shown in the cards here are converted from Torn time (TCT) to your local time"
          data-step=2>
        {{--    Dynamically inserted cards into here    --}}
    </main>

{{--    <span class="text-center inline-block w-full text-base text-purple-500">--}}
{{--        All times shown are converted from TCT to your local time automatically</span>--}}
    <h2 class="text-center text-lg">Local Time: <span class="font-medium" id="datetime"></span></h2>
    <h2 class="text-center text-lg">Torn Time: <span class="font-medium" id="datetime-tct"></span></h2>

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


    {{--  Modal for Settings Page  --}}
    <div id="modal-settings" class="modal" title="Settings">
        <div role="dialog" class="">
            <div class="settings-box">
                <form class="text-center" action="#">
                    <input class="border py-2 px-3 text-grey-darkest" style="text-align: center;" type="text" name="api-key" id="api-key" placeholder="API Key">
                    <button id="save-api-key" class="btn-filled-large settings-button" type="button">Save API Key</button>
                </form>
            </div>

            <div class="settings-divider"></div>

            <div class="settings-box">
                <form class="text-center" action="#">
                    <button id="show-tutorial-again" class="btn-filled-large settings-button" type="button">Show Tutorial Again</button>
                </form>

                <form class="text-center" id="delete-all-data">
                    <button id="delete-all-api-data" class="btn-filled-large settings-button" type="button">Delete Personal Data</button>
                </form>
            </div>
            <div class="settings-divider">
            <div class="settings-box"></div>
                <p class="ttt-author">Made By <a href="https://www.torn.com/profiles.php?XID=2254826">Fogest [2254826]</a></p>
            </div>
        </div>
    </div>
@endsection
