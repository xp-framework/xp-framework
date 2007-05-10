<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table mperson, database Ruben_Test_PS
   * (Auto-generated on Wed, 09 May 2007 14:59:38 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Mperson extends DataSet {
    public
      $person_id          = 0,
      $name               = '';
  
    private
      $_cached=   array(),
      $cacheMmessageRecipient= array(),
      $cacheMmessageAuthor= array();
  
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.mperson');
        $peer->setConnection('localhost');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'           => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE)
        ));
        $peer->setConstraints(array(
          'MmessageRecipient' => array(
            'classname' => 'de.schlund.db.rubentest.Mmessage',
            'key'       => array(
              'person_id' => 'recipient_id',
            ),
          ),
          'MmessageAuthor' => array(
            'classname' => 'de.schlund.db.rubentest.Mmessage',
            'key'       => array(
              'person_id' => 'author_id',
            ),
          ),
        ));
      }
    }  

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }
    public function _cacheGetMmessageRecipient($key) { return $this->cacheMmessageRecipient[$key]; }
    public function _cacheHasMmessageRecipient($key) { return isset($this->cacheMmessageRecipient[$key]); }
    public function _cacheAddMmessageRecipient($key, $obj) { $this->cacheMmessageRecipient[$key]= $obj; }
    public function _cacheGetMmessageAuthor($key) { return $this->cacheMmessageAuthor[$key]; }
    public function _cacheHasMmessageAuthor($key) { return isset($this->cacheMmessageAuthor[$key]); }
    public function _cacheAddMmessageAuthor($key, $obj) { $this->cacheMmessageAuthor[$key]= $obj; }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int person_id
     * @return  de.schlund.db.rubentest.Mperson entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

    /**
     * Retrieves person_id
     *
     * @return  int
     */
    public function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @param   int person_id
     * @return  int the previous value
     */
    public function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves an array of all Mmessage entities referencing
     * this entity by recipient_id=>person_id
     *
     * @return  de.schlund.db.rubentest.Mmessage[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageRecipientList() {
      if ($this->_cached['MmessageRecipient']) return array_values($this->cacheMmessageRecipient);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('recipient_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Mmessage entities referencing
     * this entity by recipient_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Mmessage>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageRecipientIterator() {
      if ($this->_cached['MmessageRecipient']) return new HashmapIterator($this->cacheMmessageRecipient);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('recipient_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Mmessage entities referencing
     * this entity by author_id=>person_id
     *
     * @return  de.schlund.db.rubentest.Mmessage[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageAuthorList() {
      if ($this->_cached['MmessageAuthor']) return array_values($this->cacheMmessageAuthor);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('author_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Mmessage entities referencing
     * this entity by author_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Mmessage>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageAuthorIterator() {
      if ($this->_cached['MmessageAuthor']) return new HashmapIterator($this->cacheMmessageAuthor);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('author_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>