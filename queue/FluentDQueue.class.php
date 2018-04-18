<?php
require_once dirname(__FILE__).'/../vendor/autoload.php';
require_once dirname(__FILE__).'/Queue.php';
/**
 * FluentD S3 implementation of Gold Lantern tracking queue
 *
 * @author dino.keco@gmail.com
 */

use Fluent\Logger\FluentLogger;

class FluentDQueue extends Queue{

  private $logger;

  private $bucket;

	public function __construct($bucket) {
    Fluent\Autoloader::register();
    $this->logger = new FluentLogger();
    $this->bucket = $bucket;
	}

	/**
	 * Add multiple items into queue table
	 *
	 * @param array $items
	 */
	public function multiqueue($items){
    foreach($items as $item){
      $this->queue($item);
    }
	}

	/**
	 * Add item into mongo queue table
	 *
	 * @param array $item
	 * @throws Exception
	 */
	public function queue($item) {
    $logger->post($this->bucket, $item);
	}

	/**
	 * Read item from mongo queue table
	 *
	 * @throws Exception
	 */
	public function dequeue($filter = null){
    // not implemented
	}

	/**
	 * Get item from mongo queue table without deleting it
	 *
	 * @return array $item - record from queue
	 * @throws Exception
	 */
	public function peek($filter = null) {
    // not implemented
	}

	/**
	 * Remove item from mongo queue table.
	 *
	 * @param string $id
	 * @throws Exception
	 */
	public function remove($id) {
    // not supported
	}

	/**
	 * Count number of messages inside of queue
	 *
	 * @return active and in the process messages
	 */
	public function count() {
    // not supported
	}
}
