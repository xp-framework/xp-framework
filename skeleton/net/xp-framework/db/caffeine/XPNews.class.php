<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
  
  /**
   * Class wrapper for table news
   * (Auto-generated on Sun,  2 Feb 2003 16:04:33 +0100 by thekid)
   *
   * Uses rdbms.ConnectionManager which is expected to have at least
   * one connection registered by name "caffeine".
   *
   * @see      xp://rdbms.DataSet
   * @purpose  Datasource accessor
   */
  class XPNews extends DataSet {
    var
      $news_id      = 0,
      $caption      = '',
      $link         = '',
      $body         = '',
      $created_at   = NULL,
      $lastchange   = NULL,
      $changedby    = '',
      $bz_id        = 0;

    /**
     * Gets an instance of this object by unique index "news_news_i_640032591"
     *
     * @model   static
     * @access  public
     * @param   int news_id
     * @return  &net.xp-framework.db.caffeine.XPNews object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByNews_id($news_id) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('caffeine', 0);
        list($data)= $db->select('
            news_id,
            caption,
            link,
            body,
            created_at,
            lastchange,
            changedby,
            bz_id
          from
            CAFFEINE..news 
          where
            news_id = %d
        ', $news_id);
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      if ($data) return new XPNews($data); else return NULL;
    }

    /**
     * Gets an array of instances of this object by bz_id
     *
     * @model   static
     * @access  public
     * @param   int bz_id
     * @return  &net.xp-framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBz_id($bz_id) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('caffeine', 0);
        $q= &$db->query('
          select
            news_id,
            caption,
            link,
            body,
            created_at,
            lastchange,
            changedby,
            bz_id
          from
            CAFFEINE..news
          where
            bz_id = %d
        ', $bz_id);
        
        $n= array();
        while ($data= $q->next()) {
          $n[]= &new XPNews($data);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $n;
    }

    /**
     * Gets an array of instances of this object descendingly ordered by created_at
     * (newest first)
     *
     * @model   static
     * @access  public
     * @param   int max default -1 maximum number of rows to get)
     * @return  &net.xp-framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByDateOrdered($max= -1) {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('caffeine', 0);
        if (-1 != $max) $db->query('set rowcount %d', $max);
        $q= &$db->query('
          select
            news_id,
            caption,
            link,
            body,
            created_at,
            lastchange,
            changedby,
            bz_id
          from
            CAFFEINE..news
          where
            bz_id = 500
          order by
            created_at desc
        ');
        
        $n= array();
        while ($data= $q->next()) {
          $n[]= &new XPNews($data);
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $n;
    }

    /**
     * Retrieves news_id
     *
     * @access  public
     * @return  int
     */
    function getNews_id() {
      return $this->news_id;
    }
      
    /**
     * Sets news_id
     *
     * @access  public
     * @param   int news_id
     * @return  int previous value
     */
    function setNews_id($news_id) {
      return $this->_change('news_id', $news_id, '%d');
    }
      
    /**
     * Retrieves caption
     *
     * @access  public
     * @return  string
     */
    function getCaption() {
      return $this->caption;
    }
      
    /**
     * Sets caption
     *
     * @access  public
     * @param   string caption
     * @return  string previous value
     */
    function setCaption($caption) {
      return $this->_change('caption', $caption, '%s');
    }
      
    /**
     * Retrieves link
     *
     * @access  public
     * @return  string
     */
    function getLink() {
      return $this->link;
    }
      
    /**
     * Sets link
     *
     * @access  public
     * @param   string link
     * @return  string previous value
     * @return  previous previous value
     */
    function setLink($link) {
      return $this->_change('link', $link, '%s');
    }
      
    /**
     * Retrieves body
     *
     * @access  public
     * @return  string
     */
    function getBody() {
      return $this->body;
    }
      
    /**
     * Sets body
     *
     * @access  public
     * @param   string body
     * @return  string previous value
     */
    function setBody($body) {
      return $this->_change('body', $body, '%s');
    }
      
    /**
     * Retrieves created_at
     *
     * @access  public
     * @return  &util.Date
     */
    function &getCreated_at() {
      return $this->created_at;
    }
      
    /**
     * Sets created_at
     *
     * @access  public
     * @param   &util.Date created_at
     * @return  &util.Date previous value
     */
    function &setCreated_at(&$created_at) {
      return $this->_change('created_at', $created_at, '%s');
    }
      
    /**
     * Retrieves lastchange
     *
     * @access  public
     * @return  &util.Date
     */
    function &getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @access  public
     * @param   &util.Date lastchange
     * @return  &util.Date previous value
     */
    function &setLastchange(&$lastchange) {
      return $this->_change('lastchange', $lastchange, '%s');
    }
      
    /**
     * Retrieves changedby
     *
     * @access  public
     * @return  string
     */
    function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @access  public
     * @param   string changedby
     * @return  string previous value
     */
    function setChangedby($changedby) {
      return $this->_change('changedby', $changedby, '%s');
    }
      
    /**
     * Retrieves bz_id
     *
     * @access  public
     * @return  int
     */
    function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @access  public
     * @param   int bz_id
     * @return  int previous value
     */
    function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id, '%d');
    }
      
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function update() {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('caffeine', 0);
        $affected= $db->update(
          'CAFFEINE..news set %c where news_id= %d',
          $this->_updated($db),
          $this->news_id
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }
    
    /**
     * Write this object to the database
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function insert() {
      $cm= &ConnectionManager::getInstance();  

      try(); {
        $db= &$cm->getByHost('caffeine', 0);
        $affected= $db->insert('CAFFEINE..news (%c)', $this->_inserted($db));
        $this->news_id= $db->identity();
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }
    
  }
?>
