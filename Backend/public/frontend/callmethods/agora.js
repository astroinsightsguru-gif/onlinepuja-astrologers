        $(document).ready(function() {
            var callDuration = {{ $callrequest->call_duration }};
            var timerInterval;
            var statusCheckInterval;

            $("#local-player-name").text("{{ astroauthcheck()['name'] }}");
            $("#remote-player-name").text("{{ $getUser['recordList'][0]['name'] }}");

            function fetchCallStatus() {
                $.ajax({
                    url: '{{ route('front.callStatus', ['callId' => $callrequest->id]) }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.call.callStatus === 'Confirmed') {
                            var updateTime = new Date(response.call.updated_at)
                        .getTime(); // Use updated_at from the response
                            startTimer(updateTime);
                            clearInterval(statusCheckInterval);
                        }
                    }
                });
            }

            function startTimer(updateTime) {
                setupFirebaseListener();
                // var currentTime = new Date().getTime();
                // var elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                // var remainingTime = callDuration - elapsedTime;
                let currentTime = remainingTime = elapsedTime='';
                $.get("{{ route('front.getDateTime') }}", function(response) {
                        // Assuming the response contains the server time in 'Y-m-d H:i:s' format
                        currentTime = new Date(response).getTime();

                        // Calculate elapsed time and remaining time
                        let elapsedTime = Math.floor((currentTime - updateTime) / 1000);
                        remainingTime = callDuration - elapsedTime;
                        // Ensure remainingTime is not negative
                        if (remainingTime < 0) {
                            remainingTime = 0;
                        }
                    // startTimer();

                    }).fail(function() {
                        console.error("Error fetching server time");
                    });

                function updateTimer() {
                    var minutes = Math.floor(remainingTime / 60);
                    var seconds = remainingTime % 60;

                    var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') +
                        seconds;
                    document.getElementById('remainingTime').innerHTML = formattedTime;
                }

                // Initial display
                updateTimer();

                timerInterval = setInterval(function() {
                    remainingTime--;
                    if (remainingTime < 0) {
                        remainingTime = 0;
                    }
                    updateTimer();

                    if (remainingTime <= 0) {
                        endCall();
                        clearInterval(timerInterval);
                    }
                }, 1000);
            }

            function setupFirebaseListener() {
                const callId = '{{ $callId }}'; // Your Laravel chat ID
                const db = firebase.firestore();
                
                // Listen to the specific document in 'updatechat' collection
                db.collection('updatecall').doc(callId)
                    .onSnapshot((doc) => {
                        if (doc.exists) {
                            const firebaseData = doc.data();
                            const newDuration = firebaseData.duration;
                            const previousDuration = callDuration;

                            // Update chatDuration
                            callDuration = newDuration;

                            // Adjust remaining time only if duration increased
                            if (callDuration > previousDuration) {
                                const additionalTime = callDuration - previousDuration;
                                remainingTime += additionalTime;
                                console.log("Added additional time from Firebase:", additionalTime);
                                
                                // Update UI immediately
                                updateTimer();
                            }
                        }
                    }, (error) => {
                    console.error("Firebase listener error:", error);
                });
            }


            // Initial status check
            fetchCallStatus();
            // Check the status every second
            statusCheckInterval = setInterval(fetchCallStatus, 2000);

            // Initial display of remaining time
            document.getElementById('remainingTime').innerHTML = formatTime(callDuration);

            function formatTime(seconds) {
                var minutes = Math.floor(seconds / 60);
                seconds = seconds % 60;
                return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }
        });



        $(document).ready(function() {
            $('#kundaliButton').click(function() {
                var userId = "{{ $userId }}"; // Fetch user ID from PHP

                // Show loading text
                $('#kundaliContent').html('<p>Loading...</p>');

                // Call the API
                $.ajax({
                    url: "{{ url('/api/kundali/getKundaliReport') }}",
                    type: "POST",
                    data: {
                        userId: userId
                    },
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response); // Debugging

                        if (!response || response.planet.status == 400 || response.planet
                            .status == 402) {
                                $('#kundaliModal').modal('show');
                            $('#kundaliContent').html('<h3 class="text-center mt-5 mb-5">No Kundali Found</h3>');
                            return;
                        }

                        // Populate modal content dynamically
                        var html = generateKundaliReportHTML(response);
                        $('#kundaliContent').html(html);

                        // ✅ Open modal only after successful API response
                        $('#kundaliModal').modal('show');
                    },
                    error: function() {
                        $('#kundaliContent').html('<p>Error fetching Kundali report.</p>');
                    }
                });
            });

            function generateKundaliReportHTML(response) {
                var html = `
            <ul class="nav nav-tabs" id="kundaliTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab"
                        aria-controls="basic" aria-selected="true">Basic Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="planetaryposition-tab" data-toggle="tab" href="#planetaryposition" role="tab"
                        aria-controls="planetaryposition" aria-selected="false">Planetary Position</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="predictions-tab" data-toggle="tab" href="#predictions" role="tab"
                        aria-controls="predictions" aria-selected="false">Predictions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="shodashvarga-tab" data-toggle="tab" href="#shodashvarga" role="tab"
                        aria-controls="shodashvarga" aria-selected="false">Shodashvarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="ashtakvarga-tab" data-toggle="tab" href="#ashtakvarga" role="tab"
                        aria-controls="ashtakvarga" aria-selected="false">Ashtakvarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="mahadasha-tab" data-toggle="tab" href="#mahadasha" role="tab"
                        aria-controls="mahadasha" aria-selected="false">Mahadasha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="yogini-tab" data-toggle="tab" href="#yogini" role="tab"
                        aria-controls="yogini" aria-selected="false">Yogini Dasha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="dosha-tab" data-toggle="tab" href="#dosha" role="tab"
                        aria-controls="dosha" aria-selected="false">Dosha</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab"
                        aria-controls="report" aria-selected="false">Report</a>
                </li>
            </ul>

            <div class="tab-content" id="kundaliTabContent">
                <!-- Basic Details Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                    ${generateBasicDetailsHTML(response)}
                </div>

                <!-- Planetary Position Tab -->
                <div class="tab-pane fade" id="planetaryposition" role="tabpanel" aria-labelledby="planetaryposition-tab">
                    ${generatePlanetaryPositionHTML(response)}
                </div>

                <!-- Predictions Tab -->
                <div class="tab-pane fade" id="predictions" role="tabpanel" aria-labelledby="predictions-tab">
                    ${generatePredictionsHTML(response)}
                </div>

                <!-- Shodashvarga Tab -->
                <div class="tab-pane fade" id="shodashvarga" role="tabpanel" aria-labelledby="shodashvarga-tab">
                    ${generateShodashvargaHTML(response)}
                </div>

                <!-- Ashtakvarga Tab -->
                <div class="tab-pane fade" id="ashtakvarga" role="tabpanel" aria-labelledby="ashtakvarga-tab">
                    ${generateAshtakvargaHTML(response)}
                </div>

                <!-- Mahadasha Tab -->
                <div class="tab-pane fade" id="mahadasha" role="tabpanel" aria-labelledby="mahadasha-tab">
                    ${generateMahadashaHTML(response)}
                </div>

                <!-- Yogini Dasha Tab -->
                <div class="tab-pane fade" id="yogini" role="tabpanel" aria-labelledby="yogini-tab">
                    ${generateYoginiDashaHTML(response)}
                </div>

                <!-- Dosha Tab -->
                <div class="tab-pane fade" id="dosha" role="tabpanel" aria-labelledby="dosha-tab">
                    ${generateDoshaHTML(response)}
                </div>

                <!-- Report Tab -->
                <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                    ${generateReportHTML(response)}
                </div>
            </div>`;

                return html;
            }

            function generateBasicDetailsHTML(response) {
                return `
            <div class="row py-3">
                <div class="col-sm-12 mt-4">
                    <div class="table-responsive table-theme shadow-pink p-3">
                        <table class="table table-bordered border-pink font-14 mb-0">
                            <tbody>
                                <tr><th class="cellhead"><b>Name</b></th><td>${response.recordList.name || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Date</b></th><td>${response.recordList.birthDate || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Time</b></th><td>${response.recordList.birthTime || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Birth Place</b></th><td>${response.recordList.birthPlace || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Latitude</b></th><td>${response.recordList.latitude || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Longitude</b></th><td>${response.recordList.longitude || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Timezone</b></th><td>${response.recordList.timezone || 'N/A'}</td></tr>
                                <tr><th class="cellhead"><b>Rasi</b></th><td>${response.planet.response.rasi || 'N/A'}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;
            }

            function generatePlanetaryPositionHTML(response) {
                if (response.planet.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var filteredData = Object.keys(response.planet.response)
                    .filter(key => !isNaN(key)) // Filter numerical keys (0 to 9)
                    .map(key => response.planet.response[key]);

                var rows = filteredData.map(planet => `
        <tr>
            <td>${planet.full_name || 'N/A'}</td>
            <td>${planet.is_combust ? 'C' : ''}</td>
            <td>${planet.retro ? 'R' : ''}</td>
            <td>${planet.zodiac || 'N/A'}</td>
            <td>${planet.local_degree || 'N/A'}</td>
            <td>${planet.global_degree || 'N/A'}</td>
            <td>${planet.nakshatra || 'N/A'}</td>
            <td>${planet.nakshatra_pada || 'N/A'}</td>
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="matchV_thead bg-pink color-red">
                            <tr>
                                <th class="cellhead">Planet</th>
                                <th class="cellhead">C</th>
                                <th class="cellhead">R</th>
                                <th class="cellhead">Rashi</th>
                                <th class="cellhead">Local Degree</th>
                                <th class="cellhead">Global Degree</th>
                                <th class="cellhead">Nakshatra</th>
                                <th class="cellhead">Pada</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generatePredictionsHTML(response) {
                if (response.personal.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var predictions = response.personal.response.map((prediction, index) => {
                    var houseNumber = index + 1;
                    var houseWord = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh',
                        'Eighth', 'Ninth', 'Tenth', 'Eleventh', 'Twelfth'
                    ][houseNumber - 1] || houseNumber;

                    return `
            <div class="panel panel-default mb-3">
                <div class="panel-heading">
                    <h3 class="panel-title mb-0">
                        <a class="accordion-toggle font-weight-semi d-block py-2 colorblack font-16" data-toggle="collapse" data-parent="#accordion" href="#accAbount_${index}">
                            ${houseWord} House
                        </a>
                    </h3>
                </div>
                <div id="accAbount_${index}" class="panel-collapse collapse ${index === 0 ? 'show' : ''}" data-parent="#accordion">
                    <div class="panel-body px-0 px-md-3 py-4 border-top">
                        <p>${prediction.personalised_prediction}</p>
                    </div>
                </div>
            </div>`;
                }).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Predictions</h2>
            </div>
            <div class="col-12">
                <div class="panel-group my-1 p-3" id="accordion">${predictions}</div>
            </div>
        </div>`;
            }

            function generateShodashvargaHTML(response) {
                if (!response.charts) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var chartNames = {
                    'D1': 'Rasi',
                    'D2': 'Hora',
                    'D3': 'Drekkana',
                    'D4': 'Chaturthamsa',
                    'D5': 'Panchamamsa',
                    'D6': 'Shastamsa',
                    'D7': 'Saptamsa',
                    'D8': 'Astamsa',
                    'D9': 'Navamsa',
                    'D10': 'Dasamsa',
                    'D11': 'Rudramsa',
                    'D12': 'Dwadasamsa',
                    'D16': 'Shodasamsa',
                    'D20': 'Vimsamsa',
                    'D24': 'Siddhamsa',
                    'D27': 'Nakshatramsa',
                    'D30': 'Trimsamsa',
                    'D40': 'Khavedamsa',
                    'D45': 'Akshavedamsa',
                    'D60': 'Shastyamsa',
                    'chalit': 'Chalit',
                    'sun': 'Sun',
                    'moon': 'Moon',
                    'kp_chalit': 'Kp Chalit'
                };

                var charts = Object.keys(response.charts).map(key => `
        <div class="col-md-4 col-sm-6 col-12 mt-3">
            <p class="font-16 mb-1"><strong>${chartNames[key] || key}</strong></p>
            <div class="svg-container">${response.charts[key]}</div>
        </div>
    `).join('');

                return `
        <h2 class="p-3">Horoscope Chart</h2>
        <div class="row p-3">${charts}</div>`;
            }

            function generateAshtakvargaHTML(response) {
                if (response.ashtakvarga.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var ashtakvargaRows = response.ashtakvarga.response.ashtakvarga_order
                    .filter(name => name !== 'Ascendant')
                    .map((name, index) => `
            <tr>
                <td>${name}</td>
                ${response.ashtakvarga.response.ashtakvarga_points[index].map(point => `<td>${point}</td>`).join('')}
            </tr>
        `).join('');

                var binnashtakvargaRows = Array.from({
                    length: 12
                }, (_, i) => `
        <tr>
            ${Object.values(response.binnashtakvarga.response).map(points => `<td>${points[i]}</td>`).join('')}
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Ashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">&nbsp;</th>
                                <th>Ar</th><th>Ta</th><th>Ge</th><th>Ca</th><th>Le</th><th>Vi</th>
                                <th>Li</th><th>Sc</th><th>Sa</th><th>Ca</th><th>Aq</th><th>Pi</th>
                            </tr>
                        </thead>
                        <tbody>${ashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h2 class="font-24 p-3">Binnashtakvarga</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                ${Object.keys(response.binnashtakvarga.response).map(name => `<th>${name}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>${binnashtakvargaRows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generateMahadashaHTML(response) {
                if (response.mahaDasha.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var mahadashaRows = response.mahaDasha.response.mahadasha.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.mahaDasha.response.mahadasha_order[index]}</td>
        </tr>
    `).join('');

                var predictions = response.mahaDashaPrediction.response.dashas.map(prediction => `
        <div class="prediction-block mb-4 p-3">
            <h4 class="font-18">${prediction.dasha} (${prediction.dasha_start_year} - ${prediction.dasha_end_year})</h4>
            <p class="font-14"><strong>Prediction:</strong> ${prediction.prediction}</p>
            <p class="font-14"><strong>Planet in Zodiac:</strong> ${prediction.planet_in_zodiac}</p>
        </div>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Mahadasha</h2>
            </div>
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">MahaDasha</th>
                                <th class="cellhead">MahaDasha Order</th>
                            </tr>
                        </thead>
                        <tbody>${mahadashaRows}</tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <h3 class="font-20 mb-2 p-3">Mahadasha Predictions</h3>
            </div>
            <div class="col-12">${predictions}</div>
        </div>`;
            }

            function generateYoginiDashaHTML(response) {
                if (response.yoginiDashaMain.status === 400) {
                    return `<p class="text-center">No Record Found</p>`;
                }

                var rows = response.yoginiDashaMain.response.dasha_list.map((dasha, index) => `
        <tr>
            <td>${dasha}</td>
            <td>${response.yoginiDashaMain.response.dasha_lord_list[index]}</td>
            <td>${response.yoginiDashaMain.response.dasha_end_dates[index]}</td>
        </tr>
    `).join('');

                return `
        <div class="row">
            <div class="col-12">
                <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead">Dasha</th>
                                <th class="cellhead">Dasha Lord</th>
                                <th class="cellhead">End Date</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
            }

            function generateDoshaHTML(response) {
                var doshas = ['mangalDosh', 'kaalsarpDosh', 'manglikDosh', 'pitraDosh', 'papasamayaDosh'];
                var doshaHTML = doshas.map(dosha => {
                    if (response[dosha].status === 400) {
                        return `<p class="text-center">No Record Found for ${dosha}</p>`;
                    }

                    return `
            <div class="col-12 mb-3">
                <div class="table-responsive table-theme shadow-pink p-3">
                    <table class="table table-bordered border-pink font-14 mb-0">
                        <thead class="font-13">
                            <tr class="bg-pink color-red font-weight-normal">
                                <th class="cellhead" colspan="2">${dosha.replace(/([A-Z])/g, ' $1').trim()}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <p>${response[dosha].response.bot_response}</p>
                                    ${response[dosha].response.remedies ? `
                                            <h5 class="font-16">Remedies</h5>
                                            <div class="dosha-remedies">
                                                ${response[dosha].response.remedies.map(remedy => `<p>${remedy}</p>`).join('')}
                                            </div>
                                        ` : ''}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
                }).join('');

                return `
        <div class="row">
            <div class="col-12">
                <h2 class="font-24 p-3">Doshas</h2>
            </div>
            ${doshaHTML}
        </div>`;
            }

            function generateReportHTML(response) {
                var ascendantReport = response.ascendantReport.status === 200 ? `
        <div class="col-12">
            <h2 class="font-24 p-3">Ascendant Report</h2>
            <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                <table class="table table-bordered border-pink font-14 mb-0">
                    <thead class="font-13">
                        <tr class="bg-pink color-red font-weight-normal">
                            <th class="cellhead">Aspect</th>
                            <th class="cellhead">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${response.ascendantReport.response.map(ascendant => `
                                <tr><td><strong>Ascendant</strong></td><td>${ascendant.ascendant}</td></tr>
                                <tr><td><strong>Ascendant Lord</strong></td><td>${ascendant.ascendant_lord}</td></tr>
                                <tr><td><strong>Ascendant Lord Location</strong></td><td>${ascendant.ascendant_lord_location} (${ascendant.ascendant_lord_house_location}th house)</td></tr>
                                <tr><td><strong>General Prediction</strong></td><td>${ascendant.general_prediction}</td></tr>
                                <tr><td><strong>Personalized Prediction</strong></td><td>${ascendant.personalised_prediction}</td></tr>
                                <tr><td><strong>Verbal Location</strong></td><td>${ascendant.verbal_location}</td></tr>
                                <tr><td><strong>Ascendant Lord Strength</strong></td><td>${ascendant.ascendant_lord_strength}</td></tr>
                                <tr><td><strong>Symbol</strong></td><td>${ascendant.symbol}</td></tr>
                                <tr><td><strong>Zodiac Characteristics</strong></td><td>${ascendant.zodiac_characteristics}</td></tr>
                                <tr><td><strong>Lucky Gem</strong></td><td>${ascendant.lucky_gem}</td></tr>
                                <tr><td><strong>Day for Fasting</strong></td><td>${ascendant.day_for_fasting}</td></tr>
                                <tr><td><strong>Gayatri Mantra</strong></td><td>${ascendant.gayatri_mantra}</td></tr>
                                <tr><td><strong>Flagship Qualities</strong></td><td>${ascendant.flagship_qualities}</td></tr>
                                <tr><td><strong>Spirituality Advice</strong></td><td>${ascendant.spirituality_advice}</td></tr>
                                <tr><td><strong>Good Qualities</strong></td><td>${ascendant.good_qualities}</td></tr>
                                <tr><td><strong>Bad Qualities</strong></td><td>${ascendant.bad_qualities}</td></tr>
                            `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    ` : `<p class="text-center">No Ascendant Record Found</p>`;

                var planetReport = Object.keys(response.planetReport).map(planet => {
                    if (response.planetReport[planet].status === 200) {
                        return response.planetReport[planet].response.map(planetDetails => `
                <div class="col-12">
                    <div class="table-responsive table-theme shadow-pink mb-3 p-3">
                        <table class="table table-bordered border-pink font-14 mb-0">
                            <thead class="font-13">
                                <tr class="bg-pink color-red font-weight-normal">
                                    <th class="cellhead">
                                                                        <th class="cellhead">${planet} Report</th>
                                    <th class="cellhead">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><strong>Planet Location</strong></td><td>${planetDetails.planet_location} (${planetDetails.planet_native_location}th house)</td></tr>
                                <tr><td><strong>Planet Zodiac</strong></td><td>${planetDetails.planet_zodiac}</td></tr>
                                <tr><td><strong>Zodiac Lord</strong></td><td>${planetDetails.zodiac_lord}</td></tr>
                                <tr><td><strong>Zodiac Lord Location</strong></td><td>${planetDetails.zodiac_lord_location} (${planetDetails.zodiac_lord_house_location}th house)</td></tr>
                                <tr><td><strong>General Prediction</strong></td><td>${planetDetails.general_prediction}</td></tr>
                                <tr><td><strong>Planet Definitions</strong></td><td>${planetDetails.planet_definitions}</td></tr>
                                <tr><td><strong>Gayatri Mantra</strong></td><td>${planetDetails.gayatri_mantra}</td></tr>
                                <tr><td><strong>Qualities Long</strong></td><td>${planetDetails.qualities_long}</td></tr>
                                <tr><td><strong>Qualities Short</strong></td><td>${planetDetails.qualities_short}</td></tr>
                                <tr><td><strong>Affliction</strong></td><td>${planetDetails.affliction}</td></tr>
                                <tr><td><strong>Personalized Prediction</strong></td><td>${planetDetails.personalised_prediction || ''}</td></tr>
                                <tr><td><strong>Verbal Location</strong></td><td>${planetDetails.verbal_location}</td></tr>
                                <tr><td><strong>Planet Zodiac Prediction</strong></td><td>${planetDetails.planet_zodiac_prediction}</td></tr>
                                <tr><td><strong>Character Keywords Positive</strong></td><td>${planetDetails.character_keywords_positive.join(', ')}</td></tr>
                                <tr><td><strong>Character Keywords Negative</strong></td><td>${planetDetails.character_keywords_negative.join(', ')}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `).join('');
                    } else {
                        return `<p class="text-center">No ${planet} Record Found</p>`;
                    }
                }).join('');

                return `
        <div class="row">
            ${ascendantReport}
            <div class="col-12 mt-4">
                <h2 class="font-24 p-3">Planet Report</h2>
                ${planetReport}
            </div>
        </div>`;
            }

        });