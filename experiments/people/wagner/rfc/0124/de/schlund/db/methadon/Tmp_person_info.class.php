<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table tmp_person_info, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Tmp_person_info extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..tmp_person_info');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'a_person_id'         => array('%d', FieldType::NUMERICN, TRUE),
          'a_mitarbeiter_id'    => array('%d', FieldType::NUMERICN, TRUE),
          'a_account_id'        => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_TMP_PERSON_INFO"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.Tmp_person_info entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return new self(array(
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(array('person_id', $person_id, EQUAL))
      ));
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
     * Retrieves a_person_id
     *
     * @return  int
     */
    public function getA_person_id() {
      return $this->a_person_id;
    }
      
    /**
     * Sets a_person_id
     *
     * @param   int a_person_id
     * @return  int the previous value
     */
    public function setA_person_id($a_person_id) {
      return $this->_change('a_person_id', $a_person_id);
    }

    /**
     * Retrieves a_mitarbeiter_id
     *
     * @return  int
     */
    public function getA_mitarbeiter_id() {
      return $this->a_mitarbeiter_id;
    }
      
    /**
     * Sets a_mitarbeiter_id
     *
     * @param   int a_mitarbeiter_id
     * @return  int the previous value
     */
    public function setA_mitarbeiter_id($a_mitarbeiter_id) {
      return $this->_change('a_mitarbeiter_id', $a_mitarbeiter_id);
    }

    /**
     * Retrieves a_account_id
     *
     * @return  int
     */
    public function getA_account_id() {
      return $this->a_account_id;
    }
      
    /**
     * Sets a_account_id
     *
     * @param   int a_account_id
     * @return  int the previous value
     */
    public function setA_account_id($a_account_id) {
      return $this->_change('a_account_id', $a_account_id);
    }
  }
?>