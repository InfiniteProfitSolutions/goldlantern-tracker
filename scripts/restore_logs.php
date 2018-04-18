<?php
define('BUFFER_SIZE', 5000);

require_once dirname(__FILE__).'/../conf.php';
require_once dirname(__FILE__).'/../queue/MongoQueue.php';

$queue = new MongoQueue(MONGO_CONNECTION_STRING, MONGO_SCHEMA, MONGO_QUEUE_COLLECTION);

$prefix = @$argv [1];

if (!isset($prefix)){
	die("No prefix!\n");
}

$files = get_log_files('log', $prefix);

if (count($files) == 0){
	echo "No files to process!\n";
}
try {
	foreach ($files as $file){
		$file_handler = fopen($file, "r");
		$items = [];
		echo 'Processing file '. $file. "\n";
		$counter = 0;
		while(!feof($file_handler)){
			$line = parse_line(fgets($file_handler));
			if ($line){
				$items[] = $line;
				$counter++;
			}
			if (count($items) == BUFFER_SIZE){
				echo "Queue Flush ".$counter."\n";
				$queue->multiqueue($items);
				$items = [];
			}
		}
		fclose($file_handler);
		if (count($items) > 0){
			$queue->multiqueue($items);
			echo "End File Flush ".$counter."\n";
		}
	}
} catch (Exception $e) {
	echo "ERROR: ".$e->getMessage()."\n";
}

function parse_line($line){
	$parts = explode("\t", $line);
	return json_decode($parts[1], true);
}

function get_log_files($dir, $prefix){
	$raw_files = scandir($dir);
	$files = [];
	foreach ($raw_files as $file){
		if (!is_dir($dir.'/'.$file) && substr($file, 0, strlen($prefix)) === $prefix){
			$files[] = $dir.'/'.$file;
		}
	}
	return $files;
}
