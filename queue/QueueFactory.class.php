<?php
require_once dirname(__FILE__).'/../conf.php';
require_once dirname(__FILE__).'/MongoQueue.class.php';
require_once dirname(__FILE__).'/FluentDQueue.class.php';

/**
* Queue Factory for initialization of different types of queues
* for Gold Lantern tracking
*/
class QueueFactory {

  /**
  * Get instance of Gold Lantern tracking queue
  */
  public static function get_instance($type = 'mongo'){
    switch($type){
      case 'mongo': return new MongoQueue(MONGO_CONNECTION_STRING, MONGO_SCHEMA, MONGO_QUEUE_COLLECTION);

      case 'fluentd': return new FluentDQueue();
    }
  }
}
?>
