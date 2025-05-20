<?php
	header("Content-type: text/event-stream");
	header("Cache-control: no-cache");

	$csvFilePath = "./data/recorded_sensor_data.csv";
	$lastModified = 0;

	while (true) {
		clearstatcache();
		$currentModified = filemtime($csvFilePath);

		if ($currentModified !== false && $currentModified > $lastModified) {
			$lastModified = $currentModified;
			echo "event: update\n";
			echo "data: new data arrived\n";
			ob_flush();
			flush();
		}

		// Sleeping briefly to reduce CPU load
		sleep(1);
	}
?>
