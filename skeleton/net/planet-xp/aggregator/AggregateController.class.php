<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.planet-xp.Feed',
    'net.planet-xp.FeedItem',
    'net.planet-xp.aggregator.RssFeed'
  );

  /**
   * Controller for the aggregation and synchronization
   * process of feeds.
   *
   * @purpose  Control aggregation
   */
  class AggregateController extends Object {
    var
      $feed_id=     0,
      $url=         '',
      $lastcheck=   NULL;
    
    var
      $feed=        NULL,
      $feedItems=   array();
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   int feed_id
     * @param   string url
     * @param   &util.Date lastcheck
     */
    function __construct($feed_id, $url, &$lastcheck) {
      $this->feed_id= $feed_id;
      $this->url= $url;
      $this->lastcheck= &$lastcheck;
    }
    
    /**
     * Fetch feed.
     *
     * @access  public
     */
    function fetch() {
    
      // TBI: switch($this->feedType) { ... }
      try(); {
        $result= &RssFeed::fetch($this->feed_id, $this->url, $this->lastcheck);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // When target has not been modified, indicate further processing
      // is not needed.
      if (TRUE === $result)
        return FALSE;
      
      // Set result into members for later processing
      $this->feed= &$result['feed'];
      $this->feedItems= &$result['feeditems'];
      return TRUE;
    }
    
    /**
     * Synchronize new data with storage.
     *
     * @access  public
     * @return  bool success
     */
    function update() {
      $cm= &ConnectionManager::getInstance();
      
      // Fetch database object and start transaction
      try(); {
        $db= &$cm->getByHost('syndicate', 0);
        $transaction= &$db->begin(new Transaction('doaggregate'));
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      // Update the feed itself
      try(); {
        $this->feed->update();
      } if (catch('SQLException', $e)) {
        $transaction->rollback();
        return throw($e);
      }
      
      // Update all its items
      try(); {
        for ($i= 0; $i < sizeof($this->feedItems); $i++) {
          if (!$this->feedItems[$i]->update()) break;
        }
      } if (catch('SQLException', $e)) {
        $transaction->rollback();
        return throw($e);
      }
      
      // Everything seems to have gone through, so commit and return
      $transaction->commit();
    }
  }
?>
