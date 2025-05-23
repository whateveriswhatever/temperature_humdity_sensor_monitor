<?php
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
	error_reporting(E_ALL);

	$recorder = "../data/recorded_sensor_data.csv";

	$rawData = file_get_contents("php://input");
	$data = json_decode($rawData, true);

	$temperature = isset($data["temperature"]) ? trim($data["temperature"]) : null;
	$humidity = isset($data["humidity"]) ? trim($data["humidity"]) : null;
    $sensor = isset($data["sensor"]) ? trim($data["sensor"]) : "default";	

	if ($temperature !== null && $humidity !== null && $sensor !== null) {
		$timestamp = date("Y-m-d H:i:s");

		// New received sensor data will be recorded inner the CSV file
		$file = fopen($recorder, 'a');

		
		if ($file !== false) {
			// Sanitize the input data
			$temperature = floatval($temperature);
			$humidity = floatval($humidity);
			$sensor = htmlspecialchars($sensor);

			fputcsv($file, [$timestamp, $temperature, $humidity, $sensor]);

			fclose($file);
			echo "Saved data successfully!";
		} else {
			http_response_code(500);
			echo "Error in opening file or lacking of data!!!";
		}
	} else {
		http_response_code(400);
		echo "Error: Missing temperature or humidity in the query parameter!!!";
	}
	
?>
