<?php
error_reporting(0);
ini_set('display_errors', 0);

date_default_timezone_set('America/Los_Angeles'); // PST timezone is default for Gold Lantern Tracking Application

define('MONGO_CONNECTION_STRING', 'mongodb://localhost:27012');
define('MONGO_SCHEMA', '');
define('MONGO_QUEUE_COLLECTION', '');
define('MAIL_TO', '');
