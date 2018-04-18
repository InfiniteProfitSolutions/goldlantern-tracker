<?php
require_once dirname(__FILE__).'/utils/GoldLanternTracker.class.php';
require_once dirname(__FILE__).'/utils/QueueFactory.class.php';
require_once dirname(__FILE__).'/utils/ZombieCookie.class.php';
require_once dirname(__FILE__).'/utils/Mailer.class.php';
require_once dirname(__FILE__).'/utils/Logger.class.php';

$tracker = new GoldLanternTracker();

// cookie generator
if (! isset ( $_COOKIE ['_gl_c_'] )) {
	try {
		$zombiCookie = new BunnyCookie();
		$zombiCookie->restoreCookie();
	} catch (Exception $e) {
		// log to warning log (invalid cookie)
		Mailer::warning("Failed to restore cookie: ".@$_COOKIE ['_gl_c_'].', reason: '. $e->getMessage());
		Logger::log("Failed to restore cookie: {".@$_COOKIE ['_gl_c_'].'}, reason: '. $e->getMessage(), 'cookies');
	}
}

$event = $tracker->parse_event();

// main processing tracking
try {
	$queue = new MongoQueue(MONGO_CONNECTION_STRING, MONGO_SCHEMA, MONGO_QUEUE_COLLECTION);
	$queue->queue($event);
} catch (Exception $e) {
	Mailer::error("Failed to capture event \n". print_r($e, true)."\n".json_encode($event, JSON_PRETTY_PRINT));
	Logger::log(json_encode($event));
}

// backup fluend tracking
try {
	$bt = new GoldLanternBatchTracker();
	$bt->process($event['_id']);
} catch (Exception $e) {
	// silent exception handling
	$event['err'] = $e->getMessage();
	Logger::log(json_encode($event), 'batch-tracker');
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
