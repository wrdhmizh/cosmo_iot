<div id="sensorDataContainer">
    <p class="text-muted">Loading sensor data...</p>
</div>

<div class="text-end mb-4">
    <small>Refreshing in <span id="countdown">15</span> seconds...</small>
</div>

<script>
let countdown = 15;
let countdownInterval;
let map = null;
let marker = null;
let charts = [];

function startCountdown() {
    clearInterval(countdownInterval);
    countdown = 15;
    document.getElementById('countdown').textContent = countdown;
    countdownInterval = setInterval(() => {
        countdown--;
        document.getElementById('countdown').textContent = countdown;
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            loadSensorData();
        }
    }, 1000);
}

function destroyCharts() {
    charts.forEach(chart => chart.destroy());
    charts = [];
}

function loadSensorData() {
    const container = document.getElementById('sensorDataContainer');
    container.innerHTML = '<p class="text-muted">Fetching latest data...</p>';

    const params = new URLSearchParams(window.location.search);
    const location = params.get('location') || 'brunei';

    fetch(`box_data.php?location=${location}`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';
            destroyCharts();

            const { name, exposure, model, currentLocation, sensors } = data;
            const [lon, lat] = currentLocation.coordinates;

            const infoHtml = `
                <div class="card mb-4">
                    <div class="card-body">
                        <h2>${name}</h2>
                        <p>Exposure: ${exposure} | Model: ${model}</p>
                        <p>Coordinates: Lat = ${lat}, Lon = ${lon}</p>
                    </div>
                </div>
                <div id="map" style="height: 400px;" class="mb-4"></div>
                <h3 class="mb-3">Sensors</h3>
                <div class="row" id="sensorCards"></div>
            `;
            container.innerHTML = infoHtml;

            if (!map) {
                map = L.map('map').setView([lat, lon], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                marker = L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();
            } else {
                map.setView([lat, lon]);
                marker.setLatLng([lat, lon]).bindPopup(name).openPopup();
            }

            const sensorCards = document.getElementById('sensorCards');

            sensors.forEach((sensor, index) => {
                const val = sensor.lastMeasurement?.value ?? 'N/A';
                const time = sensor.lastMeasurement?.createdAt ?? 'N/A';
                const chartId = `chart${index}`;

                const card = document.createElement('div');
                card.className = 'col-md-6 mb-4';
                card.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h5>${sensor.title}</h5>
                            <p>Last: ${val} ${sensor.unit}</p>
                            <p><small>${time}</small></p>
                            <canvas id="${chartId}" height="200"></canvas>
                        </div>
                    </div>
                `;
                sensorCards.appendChild(card);

                if (!isNaN(val)) {
                    const ctx = document.getElementById(chartId).getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Latest'],
                            datasets: [{
                                label: sensor.title,
                                data: [parseFloat(val)],
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    charts.push(chart);
                }
            });

            startCountdown();
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Error loading data: ${err}</div>`;
            console.error(err);
            startCountdown();
        });
}

// Initial load
loadSensorData();
</script>
