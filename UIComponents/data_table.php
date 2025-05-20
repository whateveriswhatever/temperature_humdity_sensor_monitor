<?php
	$csvFilePath = "../data/recorded_sensor_data.csv";
	$groupedData = [];

	ob_start(); // Capture output

	if (file_exists($csvFilePath)) {
		$file = fopen($csvFilePath, 'r');

		echo "
			<table>
				<thead>
					<tr>
						<th>Timestamp</th>
						<th>Temperature</th>
						<th>Humidity (%)</th>
						<th>Sensor</th>
					</tr>
				</thead>
				<tbody>
		";

		while (($row = fgetcsv($file)) !== false) {

			$timestamp = $row[0];
			$temperature = $row[1];
			$humidity = $row[2];
			$sensor = $row[3];

			echo "<tr>";
			echo "<td>" . htmlspecialchars($timestamp) . "</td>";
			echo "<td>" . htmlspecialchars($temperature) . "</td>";
			echo "<td>" . htmlspecialchars($humidity) . "</td>";
			echo "<td>" . htmlspecialchars($sensor) . "</td>";
			echo "</tr>";
			
			if (!isset($groupedData[$sensor])) {
				$groupedData[$sensor] = [
					"timestamp" => [],
					"humidities" => []
				];
			}

			$groupedData[$sensor]["timestamp"][] = $timestamp;
			$groupedData[$sensor]["humidities"][] = $humidity;
			$groupedData[$sensor]["temperature"][] = $temperature;
		}

		echo "</tbody></table>";
		fclose($file);
	} else {
		echo "<div>No data found!!!</div>";
	}

	$htmlTable = ob_get_clean();

	$response = [
		"html" => $htmlTable,
		"data" => $groupedData
	];

	header("Content-Type: application/json");
	echo json_encode($response);
?>
