<?php
require_once dirname(__FILE__).'/conf.php';
require_once dirname(__FILE__).'/utils/GoldLanternTracker.class.php';
require_once dirname(__FILE__).'/utils/Logger.class.php';

// parse event
$tracker = new GoldLanternTracker();
$event = $tracker->parse_event();

// save it into file for errors
Logger::log(json_encode($event), 'js_errors');

// render 1x1 pixel in PNG format
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: image/png');
echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
