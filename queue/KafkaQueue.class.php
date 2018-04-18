<?php
require_once dirname(__FILE__).'/Queue.php';
/**
 * Kafka implementation of Gold Lantern tracking queue
 *
 * @author dino.keco@gmail.com
 */

class KafkaQueue extends Queue{
	/**
	 * Add multiple items into kafka queue
	 *
	 * @param array $items
	 */
	public function multiqueue($items){
    foreach($items as $item){
      $this->queue($item);
    }
	}

	/**
	 * Add item into kafka queue
	 *
	 * @param array $item
	 * @throws Exception
	 */
	public function queue($item) {
    // not implemented
	}

	/**
	 * Read item from kafka queue
	 *
	 * @throws Exception
	 */
	public function dequeue($filter = null){
    // not implemented
	}

	/**
	 * Get item from kafka queue without deleting it
	 *
	 * @return array $item - record from queue
	 * @throws Exception
	 */
	public function peek($filter = null) {
    // not implemented
	}

	/**
	 * Remove item from kafka queue table.
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
