<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ConnectionManager');

  /**
   * Class wrapper for table news
   * (Auto-generated on Sun,  2 Feb 2003 16:04:33 +0100 by thekid)
   *
   * Uses rdbms.ConnectionManager which is expected to have at least
   * one connection registered by name "caffeine".
   *
   * @purpose  Datasource accessor
   */
  class XPNews extends Object {
    public
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
     * @throws  rdbms.ConnectionNotRegisteredException
     */
    public static function getByNews_id($news_id) {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('caffeine', 0);

      try {
        list($data)= $db->select('
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
            news 
          where
            news_id = %d
        ', $news_id);
      } catch (SQLException $e) {
        throw ($e);
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
     * @throws  rdbms.ConnectionNotRegisteredException
     */
    public static function getByBz_id($bz_id) {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('caffeine', 0);

      try {
        $q= $db->query('
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
            news
          where
            bz_id = %d
        ', $bz_id);
        
        $n= array();
        while ($data= $q->next()) {
          $n[]= new XPNews($data);
        }
      } catch (SQLException $e) {
        throw ($e);
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
     * @throws  rdbms.ConnectionNotRegisteredException
     */
    public static function getByDateOrdered($max= -1) {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('caffeine', 0);

      try {
        if (-1 != $max) $db->query('set rowcount %d', $max);
        $q= $db->query('
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
            news
          where
            bz_id = 500
          order by
            created_at desc
        ');
        
        $n= array();
        while ($data= $q->next()) {
          $n[]= new XPNews($data);
        }
      } catch (SQLException $e) {
        throw ($e);
      }

      return $n;
    }

    /**
     * Retrieves news_id
     *
     * @access  public
     * @return  int
     */
    public function getNews_id() {
      return $this->news_id;
    }
      
    /**
     * Sets news_id
     *
     * @access  public
     * @param   int news_id
     */
    public function setNews_id($news_id) {
      $this->news_id= $news_id;
    }
      
    /**
     * Retrieves caption
     *
     * @access  public
     * @return  string
     */
    public function getCaption() {
      return $this->caption;
    }
      
    /**
     * Sets caption
     *
     * @access  public
     * @param   string caption
     */
    public function setCaption($caption) {
      $this->caption= $caption;
    }
      
    /**
     * Retrieves link
     *
     * @access  public
     * @return  string
     */
    public function getLink() {
      return $this->link;
    }
      
    /**
     * Sets link
     *
     * @access  public
     * @param   string link
     */
    public function setLink($link) {
      $this->link= $link;
    }
      
    /**
     * Retrieves body
     *
     * @access  public
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
      
    /**
     * Sets body
     *
     * @access  public
     * @param   string body
     */
    public function setBody($body) {
      $this->body= $body;
    }
      
    /**
     * Retrieves created_at
     *
     * @access  public
     * @return  util.Date
     */
    public function getCreated_at() {
      return $this->created_at;
    }
      
    /**
     * Sets created_at
     *
     * @access  public
     * @param   util.Date created_at
     */
    public function setCreated_at(Date $created_at) {
      $this->created_at= $created_at;
    }
      
    /**
     * Retrieves lastchange
     *
     * @access  public
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @access  public
     * @param   util.Date lastchange
     */
    public function setLastchange(Date $lastchange) {
      $this->lastchange= $lastchange;
    }
      
    /**
     * Retrieves changedby
     *
     * @access  public
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @access  public
     * @param   string changedby
     */
    public function setChangedby($changedby) {
      $this->changedby= $changedby;
    }
      
    /**
     * Retrieves bz_id
     *
     * @access  public
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @access  public
     * @param   int bz_id
     */
    public function setBz_id($bz_id) {
      $this->bz_id= $bz_id;
    }
      
    /**
     * Update this object in the database
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException
     */
    public function update() {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('caffeine', 0);

      try {
        $affected= $db->update('
          news set
            caption = %s,
            link = %s,
            body = %s,
            created_at = %s,
            lastchange = %s,
            changedby = %s,
            bz_id = %d
          where
            news_id= %d
          ',
          $this->caption,
          $this->link,
          $this->body,
          $this->created_at,
          $this->lastchange,
          $this->changedby,
          $this->bz_id,
          $this->news_id
        );
      } catch (SQLException $e) {
        throw ($e);
      }

      return $affected;
    }
    
    /**
     * Write this object to the database
     *
     * @access  public
     * @return  int affected rows
     * @throws  rdbms.SQLException in case an error occurs
     * @throws  rdbms.ConnectionNotRegisteredException
     */
    public function insert() {
      $cm= ConnectionManager::getInstance();  
      $db= $cm->getByHost('caffeine', 0);

      try {
        $affected= $db->insert('
          news (
            caption,
            link,
            body,
            created_at,
            lastchange,
            changedby,
            bz_id
          ) values (
            %s, %s, %s, %s, %s, %s, %d
          )',
          $this->news_id,
          $this->caption,
          $this->link,
          $this->body,
          $this->created_at,
          $this->lastchange,
          $this->changedby,
          $this->bz_id
        );
        $this->news_id= $db->identity();
      } catch (SQLException $e) {
        throw ($e);
      }

      return $affected;
    }
    
  }
?>
