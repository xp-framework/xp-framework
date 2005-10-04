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
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() {
      with ($peer= &XPNews::getPeer()); {
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
     * @access  public
     * @return  &rdbms.Peer
     */
    function &getPeer() {
      return Peer::forName(__CLASS__);
    }

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
      $peer= &XPNews::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('news_id', $news_id, EQUAL))));
    }

    /**
     * Gets an array of instances of this object by bz_id
     *
     * @model   static
     * @access  public
     * @param   int bz_id
     * @return  net.xp-framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    function getByBz_id($bz_id) {
      $peer= &XPNews::getPeer();
      return $peer->doSelect(new Criteria(array('bz_id', $bz_id, EQUAL)));
    }

    /**
     * Gets an array of instances of this object descendingly ordered by created_at
     * (newest first)
     *
     * @model   static
     * @access  public
     * @param   int max default 0 maximum number of rows to get)
     * @return  net.xp-framework.db.caffeine.XPNews[] objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    function getByDateOrdered($max= 0) {
      $peer= &XPNews::getPeer();
      with ($c= &new Criteria()); {
        $c->add('bz_id', 500, EQUAL);
        $c->addOrderBy('created_at', DESCENDING);
      }
      
      return $peer->doSelect($c, $max);
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
      return $this->_change('news_id', $news_id);
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
      return $this->_change('caption', $caption);
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
      return $this->_change('link', $link);
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
      return $this->_change('body', $body);
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
      return $this->_change('created_at', $created_at);
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
      return $this->_change('lastchange', $lastchange);
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
      return $this->_change('changedby', $changedby);
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
      return $this->_change('bz_id', $bz_id);
    }
  }
?>
