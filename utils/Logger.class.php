<?php
/**
* Logger class used to save Gold Lantern tracking events to file.
* Logger has autoroll option to split log files into chucks of 64 MB
*
* @author dino.keco@gmail.com
*/
class Logger{

	const SIZE = 67108864; // 64MB

	const FOLDER = 'log';

	const FILE_PREFIX = 'tracker';

	const FILE_EXT = '.txt';

	const DATE_FORMAT = 'Y-m-d H:i:s';

	public static function log($line, $prefix=null){
		$filename = self::FOLDER.'/'.(isset($prefix) ? $prefix : self::FILE_PREFIX).self::FILE_EXT;
		if (file_exists($filename) && filesize($filename) >= self::$SIZE){
			$new_filename = self::FOLDER.'/'.(isset($prefix) ? $prefix : self::FILE_PREFIX).'-'.date(self::DATE_FORMAT).self::FILE_EXT;
			rename($filename, $new_filename);
		}
		file_put_contents($filename, date(self::DATE_FORMAT). "\t" . $line . PHP_EOL, FILE_APPEND);
	}
}
