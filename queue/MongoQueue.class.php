<?php
require_once dirname(__FILE__).'/Queue.php';

if (! class_exists ( 'Mongo' )) exit ( 'No Mongo DB driver installed' );

/**
 * Mongo implementation of Gold Lantern Tracking queue
 *
 * @author dino.keco@gmail.com
 */
class MongoQueue extends Queue{

	private $collection;

	public function __construct($connection_string, $database, $collection) {
		try {
			$connection = new MongoClient ( $connection_string, ['connectTimeoutMS' => '1000'] );
			$connection->setReadPreference ( MongoClient::RP_PRIMARY_PREFERRED, []);
			$this->collection = $connection->selectCollection ( $database, $collection );
		} catch ( Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Add multiple items into queue table
	 *
	 * @param array $items
	 */
	public function multiqueue($items){
		for ($i=1; $i<4; $i++){
			try {
				$batch = new MongoUpdateBatch($this->collection, ['socketTimeoutMS' => -1, 'wTimeoutMS' => 0]);
				foreach ($items as $item){
					$batch->add([
							'q' => ['_id' => $item['_id'], 'client' => $item['client']],
							'u' => ['$set' => $item],
							'upsert' => true
					]);
				}
				$batch->execute();
				return;
			} catch (Exception $e) {
				echo "Failed to multiqueue. waiting for ". ($i*3)." seconds for retry...";
			}
		}
	}

	/**
	 * Add item into mongo queue table
	 *
	 * @param array $item
	 * @throws Exception
	 */
	public function queue($item) {
		if ($this->collection){
			$this->collection->insert ( $item , ['socketTimeoutMS' => -1, 'wTimeoutMS' => 0, 'w' =>1]);
		}
	}

	/**
	 * Read item from mongo queue table
	 *
	 * @throws Exception
	 */
	public function dequeue($filter = null){
		try {
			return $this->collection->findOne ( $filter );
		} catch ( Exception $e ) {
			throw new Exception ( 'Failed to get item from Mongo Queue', 0, $e );
		}
	}

	/**
	 * Get item from mongo queue table without deleting it
	 *
	 * @return array $item - record from queue
	 * @throws Exception
	 */
	public function peek($filter = null) {
		try {
			$default = array (
					'field' => '_mqs',
					'search' => null,
					'update' => 1
			);
			$filters = array_merge ( $default, $filter );

			foreach ( $filters as $f ) {
				$query [] [$f ['field']] = $f ['search'];
				$update [] [$f ['field']] = $f ['update'];
			}

			$doc = $this->collection->findAndModify ( $query, array (
					'$set' => $update
			) );
			return $doc;
		} catch ( Exception $e ) {
			throw new Exception ( 'Failed to get item from Mongo Queue', 0, $e );
		}
	}

	/**
	 * Remove item from mongo queue table.
	 *
	 * @param string $id
	 * @throws Exception
	 */
	public function remove($id) {
		try {
			return $this->collection->remove ( array (
					'_id' => $id
			) );
		} catch ( Exception $e ) {
			throw new Exception ( 'Failed to remove item from Mongo Queue', 0, $e );
		}
	}

	/**
	 * Count number of messages inside of queue
	 *
	 * @return active and in the process messages
	 */
	public function count() {
		try {
			$pending = $this->collection->count ( array (
					'_mqs' => null
			) );
			$process = $this->collection->count ( array (
					'_mqs' => '1'
			) );
			return array (
					'pending' => $pending,
					'process' => $process
			);
		} catch ( Exception $e ) {
			throw new Exception ( 'Failed to count items in Mongo Queue', 0, $e );
		}
	}
}
