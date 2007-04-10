<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table gulp_allocation, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Gulp_allocation extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..gulp_allocation');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('person_id', 'account_id'));
        $peer->setTypes(array(
          'account_name'        => array('%s', FieldType::VARCHAR, TRUE),
          'account_pw'          => array('%s', FieldType::VARCHAR, TRUE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'account_id'          => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_ALLOC"
     * 
     * @param   int person_id
     * @param   int account_id
     * @return  de.schlund.db.methadon.Gulp_allocation entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_idAccount_id($person_id, $account_id) {
      return new self(array(
        'person_id'  => $person_id,
        'account_id'  => $account_id,
        '_loadCrit' => new Criteria(
          array('person_id', $person_id, EQUAL),
          array('account_id', $account_id, EQUAL)
        )
      ));
    }

    /**
     * Retrieves account_name
     *
     * @return  string
     */
    public function getAccount_name() {
      return $this->account_name;
    }
      
    /**
     * Sets account_name
     *
     * @param   string account_name
     * @return  string the previous value
     */
    public function setAccount_name($account_name) {
      return $this->_change('account_name', $account_name);
    }

    /**
     * Retrieves account_pw
     *
     * @return  string
     */
    public function getAccount_pw() {
      return $this->account_pw;
    }
      
    /**
     * Sets account_pw
     *
     * @param   string account_pw
     * @return  string the previous value
     */
    public function setAccount_pw($account_pw) {
      return $this->_change('account_pw', $account_pw);
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
     * Retrieves account_id
     *
     * @return  int
     */
    public function getAccount_id() {
      return $this->account_id;
    }
      
    /**
     * Sets account_id
     *
     * @param   int account_id
     * @return  int the previous value
     */
    public function setAccount_id($account_id) {
      return $this->_change('account_id', $account_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>