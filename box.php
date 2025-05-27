<?php
$boxIds = [
  '6432c58260cfd90007a3a792',
  '58f71187e617ed0011e5bdee' // Remaining boxes only
];
?>
<div id="senseBoxContent"></div>
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
 
<style>
  .chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
    margin-top: 10px;
  }
 
  .chart-card {
    background: white;
    border-radius: 6px;
    padding: 8px;
    margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
  }
 
  .chart-card h4 {
    margin: 0;
    font-size: 14px;
  }
 
  .chart-card p {
    margin: 4px 0;
    font-size: 12px;
  }
 
  .chart-container {
    height: 100px;
    position: relative;
    margin-top: 8px;
  }
 
  .map-container {
    height: 250px;
    margin-top: 10px;
  }
 
  #countdown {
    text-align: right;
    margin: 5px 0 10px;
    color: #555;
    font-size: 13px;
    font-style: italic;
  }
</style>
 
<div id="countdown">Refreshing in <span id="timer">15</span> seconds...</div>
 
<script>
const boxIds = <?php echo json_encode($boxIds); ?>;
 
function loadAllBoxes() {
  const container = document.getElementById("senseBoxContent");
  container.innerHTML = "";
 
  boxIds.forEach((boxId, boxIndex) => {
    fetch(`https://api.opensensemap.org/boxes/${boxId}`)
      .then(res => res.json())
      .then(data => {
        const boxName = data.name;
        const exposure = data.exposure;
        const coords = data.loc[0]?.geometry?.coordinates || [];
        const lat = coords[1];
        const lng = coords[0];
 
        // Info card
        const info = document.createElement("div");
        info.className = "chart-card";
        info.innerHTML = `
          <h4>${boxName}</h4>
          <p><strong>Exposure:</strong> ${exposure}</p>
          ${lat && lng ? `<p>Lat: ${lat} | Lng: ${lng}</p>` : ""}
        `;
        container.appendChild(info);
 
        // Charts
        const grid = document.createElement("div");
        grid.className = "chart-grid";
 
        let chartCount = 0;
        data.sensors.forEach(sensor => {
          if (!sensor.lastMeasurement) return;
 
          const title = sensor.title;
          const unit = sensor.unit;
          const value = sensor.lastMeasurement.value;
          const chartId = `chart_${boxIndex}_${chartCount}`;
 
          const card = document.createElement("div");
          card.className = "chart-card";
          card.innerHTML = `
            <h4>${title}</h4>
            <p><strong>${value}</strong> ${unit}</p>
            <div class="chart-container"><canvas id="${chartId}"></canvas></div>
          `;
          grid.appendChild(card);
 
          setTimeout(() => {
            new Chart(document.getElementById(chartId).getContext("2d"), {
              type: "bar",
              data: {
                labels: [""],
                datasets: [{
                  label: title,
                  data: [value],
                  backgroundColor: "rgba(54, 162, 235, 0.6)",
                  borderColor: "rgba(54, 162, 235, 1)",
                  borderWidth: 1
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
              }
            });
          }, 100);
 
          chartCount++;
        });
 
        container.appendChild(grid);
 
        // Map
        if (lat && lng) {
          const mapContainerId = `map_${boxIndex}`;
          const mapCard = document.createElement("div");
          mapCard.className = "chart-card";
          mapCard.innerHTML = `<h4>Map Location</h4><div id="${mapContainerId}" class="map-container"></div>`;
          container.appendChild(mapCard);
 
          setTimeout(() => {
            const map = L.map(mapContainerId).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup(boxName).openPopup();
          }, 100);
        }
      });
  });
}
 
// Countdown & auto-refresh
let countdown = 15;
const timerEl = document.getElementById("timer");
 
function startCountdown() {
  setInterval(() => {
    countdown--;
    timerEl.textContent = countdown;
    if (countdown === 0) {
      countdown = 15;
      loadAllBoxes();
    }
  }, 1000);
}
 
loadAllBoxes();
startCountdown();
</script>
 
 