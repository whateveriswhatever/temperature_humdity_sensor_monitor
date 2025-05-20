<!DOCTYPE html>
<html>
<head>
	<title>Humidity Sensor Dashboard</title>
	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			margin: 0;
			padding: 0;
			background-color: #f9f9f9;
			color: #333;
		}

		h2 {
			text-align: center;
			margin-top: 40px;
		}

		.container {
			max-width: 1200px;
			margin: auto;
			padding: 20px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			background-color: #fff;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
			table-layout: auto
		}

		th, td {
			padding: 12px;
			border: 1px solid #ddd;
			text-align: center;
		}

		th {
			background-color: #f0f0f0;
		}

		canvas {
			width: 100% !important;
			height: auto !important;
			margin: 20px 0;
		}

		@media screen and (max-width: 768px) {
			table, thead, tbody, th, td, tr {
				display: block;
			}

			tr {
				margin-bottom: 15px;
			}

			th {
				display: none;
			}	

			td 	{
				position: relative;
				padding-left: 50%;
				text-align: left;
			}

			td::before {
				position: absolute;
				left: 10px;
				width: 45%;
				white-space: nowrap;
				font-weight: bold;
				color: #888;
			}

			td:nth-of-type(1)::before { content: "Timestamp"; }
			td:nth-of-type(2)::before { content: "Temperature"; }
			td:nth-of-type(3)::before { content: "Humidity (%)"; }
			td:nth-of-type(4)::before { content: "Sensor"; }

			.table-container {
				overflow-x: auto,
				overflow-y: auto,
				max-width: 100%,
				max-height: 400px,
				border: 1px solid #ccc
			} 
		}


	</style>
</head>
<body>
	<div class="container"> 
		<h2>History</h2>
		<div class="table-wrapper">
			<div id="table-container"></div>
		</div>
		

		<h2>Humidity Over Time</h2>
		<canvas id="humidityChart"></canvas>

		<h2>Temperature Over Time</h2>
		<canvas id="temperatureChart"></canvas>

	</div>
	
	<script>
		let chart;
		let temperatureChart;

		window.humidityChart = null;
		window.temperatureChart = null;

		const colors = [
			"rgb(75, 192, 192)",
			"rgb(255, 99, 132)",
			"rgb(54, 162, 235)",
			"rgb(255, 206, 86)",
			"rgb(153, 102, 255)"
		];
		
		const renderLineChart = (canvasId, dataBySensor, valueKey, label, yAxisLabel, baseHue = 0) => {
			const context = document.getElementById(canvasId).getContext("2d");
			const datasets = Object.entries(dataBySensor).map(([sensor, data], index) => {
				const color = `hsl(${baseHue + index * 60}, 70%, 50%)`;

				return {
					label: `${sensor}`,
					data: data[valueKey].map((v) => parseFloat(v.trim())),
					fill: false,
					borderColor: color,
					tension: 0.3,
					pointRadius: 3
				};
			});

			const firstSensorKey = Object.keys(dataBySensor)[0];
			const labels = dataBySensor[firstSensorKey]?.timestamp || [];

			if (canvasId === "humidityChart" && window.humidityChart instanceof Chart) {
				window.humidityChart.destroy();
			}

			if (canvasId === "temperatureChart" && window.temperatureChart instanceof Chart) {
				window.temperatureChart.destroy();
			}

			const chart = new Chart(context, {
				type: "line",
				data: {
					labels,
					datasets
				},
				options: {
					responsive: true,
					animation: {
						duration: 1000,
						easing: "easeInOutCubic",
						delay: (context) => context.datasetIndex * 300
					},
					plugins: {
						legend: {
							display: true,
							position: "top"
						},
						title: {
							display: true,
							text: label
						}
					},
					scales: {
						x: {
							title: {display: true, text: "Timestamp"}
						},
						y: {
							title: {display: true, text: yAxisLabel},
							beginAtZero: false
						}
					}
				}
			});

			if (canvasId === "humidityChart") window.humidityChart = chart;
			else if (canvasId === "temperatureChart") window.temperatureChart = chart;
		};

		function updateUI() {
			fetch("./UIComponents/data_table.php")
				.then((res) => res.json())
				.then((json) => {
					document.getElementById("table-container").innerHTML = json.html;
					renderLineChart("humidityChart", json["data"], "humidities", "Humidity Over Time by Sensor", "Humidity (%)", 0);
					renderLineChart("temperatureChart", json["data"], "temperature", "Temperature Over Time by Sensor", "Celsius (C)", 180);
				});
		}

		// Initial load
		updateUI();

		// SSE (Server-Sent Event) for real-time update
		const eventSource = new EventSource("stream.php");
		eventSource.addEventListener("update", () => {
			console.log("New data has just arrived!");
			updateUI();
		});
	</script>
</body>
