<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Class representing an planet-xp.net feed.
   *
   * @purpose  Aggregate feeds
   */
  class Feed extends Object {
    var
      $feed_id=     0,
      $title=       '',
      $link=        '',
      $description= '';
    
    var
      $_children=   array();
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   int feed_id
     * @param   string title
     * @param   string link
     * @param   string description
     */
    function __construct($feed_id, $title, $link, $description) {
      $this->feed_id= $feed_id;
      $this->title= $title;
      $this->link= $link;
      $this->description= $description;
    }
    
    /**
     * Synchronizes the feed with the database.
     *
     * @access  public
     * @return  int affected_rows
     */
    function update() {
      $cm= &ConnectionManager::getInstance();
      try(); {
        $db= &$cm->getByHost('syndicate', 0);
        
        $cnt= $db->update('
            syndicate.feed
          set
            title= %s,
            link= %s,
            description= %s,
            lastcheck= %s,
            lastchange= %s,
            changedby= %s
          where feed_id= %d',
          $this->title,
          $this->link,
          $this->description,
          Date::now(),
          Date::now(),
          __CLASS__,
          $this->feed_id
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      return $cnt;
    }
  }
?>
