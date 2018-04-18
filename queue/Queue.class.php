<?php
/**
 * Abstract Class for Queue
 *
 * @author dino.keco@gmail.com
 */
abstract class Queue {

	/**
	 * Add multiple items into queue table
	 *
	 * @param array $items
	 */
	abstract public function multiqueue($items);

	/**
	 * Add item into queue
	 *
	 * @param array $item
	 */
	abstract public function queue($item);

  /**
	 * Read item from queue
	 *
	 * @param array $item
	 */
	abstract public function dequeue($filter = null);

	/**
	 * Get item from queue table without deleting it
	 *
	 * @return array $item - record from queue
	 */
	abstract public function peek($filter = null);

	/**
	 * Remove item from queue.
	 *
	 * @param string $id
	 */
	abstract public function remove($id);

	/**
	 * Count number of messages inside of queue
	 *
	 * @return active and in the process messages
	 */
	abstract public function count();
}
