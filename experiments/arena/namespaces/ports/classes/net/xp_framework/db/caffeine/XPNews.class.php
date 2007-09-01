<?php
/* This class is part of the XP framework
 *
 * $Id: XPNews.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::db::caffeine;
 
  ::uses('rdbms.DataSet');
  
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
  class XPNews extends rdbms::DataSet {
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
     * Static initializer
     *
     */
    public static function __static() {
      with ($peer= ::getPeer()); {
        $peer->setTable('CAFFEINE..news');
        $peer->setConnection('caffeine');
        $peer->setIdentity('news_id');
        $peer->setPrimary(array('news_id'));
        $peer->setTypes(array(
          'news_id'      => '%d',
          'caption'      => '%s',
          'link'         => '%s',
          'body'         => '%s',
          'created_at'   => '%s',
          'lastchange'   => '%s',
          'changedby'    => '%s',
          'bz_id'        => '%d',
        ));
      }
    }
    
    /**
     * Retrieve associated peer
     *
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }

    /**
     * Gets an instance of this object by unique index "news_news_i_640032591"
     *
     * @param   int news_id
     * @return  &net.xp_framework.db.caffeine.XPNews object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByNews_id($news_id) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new (array('news_id', $news_id, EQUAL))));
    }

    /**
     * Gets an array of instances of this object by bz_id
     *
     * @param   int bz_id
     * @return  net.xp_framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      $peer= ::getPeer();
      return $peer->doSelect(new (array('bz_id', $bz_id, EQUAL)));
    }

    /**
     * Gets an array of instances of this object descendingly ordered by created_at
     * (newest first)
     *
     * @param   int max default 0 maximum number of rows to get)
     * @return  net.xp_framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDateOrdered($max= 0) {
      $peer= ::getPeer();
      with ($c= new ()); {
        $c->add('bz_id', 500, EQUAL);
        $c->addOrderBy('created_at', DESCENDING);
      }
      
      return $peer->doSelect($c, $max);
    }

    /**
     * Retrieves news_id
     *
     * @return  int
     */
    public function getNews_id() {
      return $this->news_id;
    }
      
    /**
     * Sets news_id
     *
     * @param   int news_id
     * @return  int previous value
     */
    public function setNews_id($news_id) {
      return $this->_change('news_id', $news_id);
    }
      
    /**
     * Retrieves caption
     *
     * @return  string
     */
    public function getCaption() {
      return $this->caption;
    }
      
    /**
     * Sets caption
     *
     * @param   string caption
     * @return  string previous value
     */
    public function setCaption($caption) {
      return $this->_change('caption', $caption);
    }
      
    /**
     * Retrieves link
     *
     * @return  string
     */
    public function getLink() {
      return $this->link;
    }
      
    /**
     * Sets link
     *
     * @param   string link
     * @return  string previous value
     * @return  previous previous value
     */
    public function setLink($link) {
      return $this->_change('link', $link);
    }
      
    /**
     * Retrieves body
     *
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
      
    /**
     * Sets body
     *
     * @param   string body
     * @return  string previous value
     */
    public function setBody($body) {
      return $this->_change('body', $body);
    }
      
    /**
     * Retrieves created_at
     *
     * @return  &util.Date
     */
    public function getCreated_at() {
      return $this->created_at;
    }
      
    /**
     * Sets created_at
     *
     * @param   &util.Date created_at
     * @return  &util.Date previous value
     */
    public function setCreated_at($created_at) {
      return $this->_change('created_at', $created_at);
    }
      
    /**
     * Retrieves lastchange
     *
     * @return  &util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   &util.Date lastchange
     * @return  &util.Date previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }
      
    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }
      
    /**
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }
  }
?>
