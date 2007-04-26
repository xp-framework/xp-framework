<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table email_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Email_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..email_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('email_type_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'email_type_id'       => array('%d', FieldType::NUMERIC, FALSE)
        ));
      }
    }  

    function __get($name) {
      $this->load();
      return $this->get($name);
    }

    function __sleep() {
      $this->load();
      return array_merge(array_keys(self::getPeer()->types), array('_new', '_changed'));
    }

    /**
     * force loading this entity from database
     *
     */
    public function load() {
      if ($this->_isLoaded) return;
      $this->_isLoaded= true;
      $e= self::getPeer()->doSelect($this->_loadCrit);
      if (!$e) return;
      foreach (array_keys(self::getPeer()->types) as $p) {
        if (isset($this->{$p})) continue;
        $this->{$p}= $e[0]->$p;
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
     * Gets an instance of this object by index "PK_EMAIL_TYPE"
     * 
     * @param   int email_type_id
     * @return  de.schlund.db.methadon.Email_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEmail_type_id($email_type_id) {
      return new self(array(
        'email_type_id'  => $email_type_id,
        '_loadCrit' => new Criteria(array('email_type_id', $email_type_id, EQUAL))
      ));
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves email_type_id
     *
     * @return  int
     */
    public function getEmail_type_id() {
      return $this->email_type_id;
    }
      
    /**
     * Sets email_type_id
     *
     * @param   int email_type_id
     * @return  int the previous value
     */
    public function setEmail_type_id($email_type_id) {
      return $this->_change('email_type_id', $email_type_id);
    }

    /**
     * Retrieves an array of all Email entities referencing
     * this entity by email_type_id=>email_type_id
     *
     * @return  de.schlund.db.methadon.Email[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailEmail_typeList() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('email_type_id', $this->getEmail_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email entities referencing
     * this entity by email_type_id=>email_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailEmail_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('email_type_id', $this->getEmail_type_id(), EQUAL)
      ));
    }
  }
?>