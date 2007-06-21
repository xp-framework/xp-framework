<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 10625 2007-06-15 15:04:07Z friebe $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table mperson, database Ruben_Test_PS
   * (Auto-generated on Wed, 20 Jun 2007 08:56:00 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Mperson extends DataSet {
    public
      $person_id          = 0,
      $name               = '';
  
    protected
      $cache= array(
        'MmessageAuthor' => array(),
        'MmessageRecipient' => array(),
      );

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
        $peer->setRelations(array(
          'MmessageAuthor' => array(
            'classname' => 'de.schlund.db.rubentest.Mmessage',
            'key'       => array(
              'person_id' => 'author_id',
            ),
          ),
          'MmessageRecipient' => array(
            'classname' => 'de.schlund.db.rubentest.Mmessage',
            'key'       => array(
              'person_id' => 'recipient_id',
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
     * @throws  lang.IllegalArumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
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
      return $r ? $r[0] : NULL;
    }

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
     * this entity by author_id=>person_id
     *
     * @return  de.schlund.db.rubentest.Mmessage[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageAuthorList() {
      if ($this->cached['MmessageAuthor']) return array_values($this->cache['MmessageAuthor']);
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
      if ($this->cached['MmessageAuthor']) return new HashmapIterator($this->cache['MmessageAuthor']);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('author_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Mmessage entities referencing
     * this entity by recipient_id=>person_id
     *
     * @return  de.schlund.db.rubentest.Mmessage[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMmessageRecipientList() {
      if ($this->cached['MmessageRecipient']) return array_values($this->cache['MmessageRecipient']);
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
      if ($this->cached['MmessageRecipient']) return new HashmapIterator($this->cache['MmessageRecipient']);
      return XPClass::forName('de.schlund.db.rubentest.Mmessage')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('recipient_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>