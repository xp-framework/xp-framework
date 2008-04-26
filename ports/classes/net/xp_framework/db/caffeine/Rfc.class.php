<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table rfc, database CAFFEINE
   * (This class was auto-generated, so please do not change manually)
   *
   * @purpose  Datasource accessor
   */
  class Rfc extends DataSet {
    public
      $rfc_id             = 0,
      $title              = '',
      $author_id          = 0,
      $created_at         = NULL,
      $status             = '',
      $content            = NULL,
      $lastchange         = NULL,
      $changedby          = '',
      $bz_id              = 0;
  
    protected
      $cache= array(
        'Author' => array(),
        'ContributorRfc' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('CAFFEINE.rfc');
        $peer->setConnection('caffeine');
        $peer->setPrimary(array('rfc_id'));
        $peer->setTypes(array(
          'rfc_id'              => array('%d', FieldType::INT, FALSE),
          'title'               => array('%s', FieldType::VARCHAR, FALSE),
          'author_id'           => array('%d', FieldType::INT, FALSE),
          'created_at'          => array('%s', FieldType::DATETIME, FALSE),
          'status'              => array('%s', FieldType::VARCHAR, FALSE),
          'content'             => array('%s', FieldType::TEXT, TRUE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Author' => array(
            'classname' => 'net.xp_framework.db.caffeine.Person',
            'key'       => array(
              'author_id' => 'person_id',
            ),
          ),
          'ContributorRfc' => array(
            'classname' => 'net.xp_framework.db.caffeine.Contributor',
            'key'       => array(
              'rfc_id' => 'rfc_id',
            ),
          ),
        ));
      }
    }  

    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArgumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int rfc_id
     * @return  net.xp_framework.db.caffeine.Rfc entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRfc_id($rfc_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('rfc_id', $rfc_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "rfc_Fauthor"
     * 
     * @param   int author_id
     * @return  net.xp_framework.db.caffeine.Rfc[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAuthor_id($author_id) {
      return self::getPeer()->doSelect(new Criteria(array('author_id', $author_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "user_Kbz"
     * 
     * @param   int bz_id
     * @return  net.xp_framework.db.caffeine.Rfc[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      return self::getPeer()->doSelect(new Criteria(array('bz_id', $bz_id, EQUAL)));
    }

    /**
     * Retrieves rfc_id
     *
     * @return  int
     */
    public function getRfc_id() {
      return $this->rfc_id;
    }
      
    /**
     * Sets rfc_id
     *
     * @param   int rfc_id
     * @return  int the previous value
     */
    public function setRfc_id($rfc_id) {
      return $this->_change('rfc_id', $rfc_id);
    }

    /**
     * Retrieves title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
    }

    /**
     * Retrieves author_id
     *
     * @return  int
     */
    public function getAuthor_id() {
      return $this->author_id;
    }
      
    /**
     * Sets author_id
     *
     * @param   int author_id
     * @return  int the previous value
     */
    public function setAuthor_id($author_id) {
      return $this->_change('author_id', $author_id);
    }

    /**
     * Retrieves created_at
     *
     * @return  util.Date
     */
    public function getCreated_at() {
      return $this->created_at;
    }
      
    /**
     * Sets created_at
     *
     * @param   util.Date created_at
     * @return  util.Date the previous value
     */
    public function setCreated_at($created_at) {
      return $this->_change('created_at', $created_at);
    }

    /**
     * Retrieves status
     *
     * @return  string
     */
    public function getStatus() {
      return $this->status;
    }
      
    /**
     * Sets status
     *
     * @param   string status
     * @return  string the previous value
     */
    public function setStatus($status) {
      return $this->_change('status', $status);
    }

    /**
     * Retrieves content
     *
     * @return  string
     */
    public function getContent() {
      return $this->content;
    }
      
    /**
     * Sets content
     *
     * @param   string content
     * @return  string the previous value
     */
    public function setContent($content) {
      return $this->_change('content', $content);
    }

    /**
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
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
     * @return  string the previous value
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
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>author_id
     *
     * @return  net.xp_framework.db.caffeine.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAuthor() {
      $r= ($this->cached['Author']) ?
        array_values($this->cache['Author']) :
        XPClass::forName('net.xp_framework.db.caffeine.Person')
          ->getMethod('getPeer')
          ->invoke(NULL)
          ->doSelect(new Criteria(
          array('person_id', $this->getAuthor_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Contributor entities referencing
     * this entity by rfc_id=>rfc_id
     *
     * @return  net.xp_framework.db.caffeine.Contributor[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getContributorRfcList() {
      if ($this->cached['ContributorRfc']) return array_values($this->cache['ContributorRfc']);
      return XPClass::forName('net.xp_framework.db.caffeine.Contributor')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->doSelect(new Criteria(
          array('rfc_id', $this->getRfc_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Contributor entities referencing
     * this entity by rfc_id=>rfc_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.db.caffeine.Contributor>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getContributorRfcIterator() {
      if ($this->cached['ContributorRfc']) return new HashmapIterator($this->cache['ContributorRfc']);
      return XPClass::forName('net.xp_framework.db.caffeine.Contributor')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->iteratorFor(new Criteria(
          array('rfc_id', $this->getRfc_id(), EQUAL)
      ));
    }
  }
?>