<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table external_password, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class External_password extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..external_password');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('person_id', 'crypt_type_id'));
        $peer->setTypes(array(
          'password'            => array('%s', FieldType::VARCHAR, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'crypt_type_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_PASSWORD"
     * 
     * @param   int person_id
     * @param   int crypt_type_id
     * @return  de.schlund.db.methadon.External_password entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_idCrypt_type_id($person_id, $crypt_type_id) {
      return new self(array(
        'person_id'  => $person_id,
        'crypt_type_id'  => $crypt_type_id,
        '_loadCrit' => new Criteria(
          array('person_id', $person_id, EQUAL),
          array('crypt_type_id', $crypt_type_id, EQUAL)
        )
      ));
    }

    /**
     * Retrieves password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->password;
    }
      
    /**
     * Sets password
     *
     * @param   string password
     * @return  string the previous value
     */
    public function setPassword($password) {
      return $this->_change('password', $password);
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
     * Retrieves crypt_type_id
     *
     * @return  int
     */
    public function getCrypt_type_id() {
      return $this->crypt_type_id;
    }
      
    /**
     * Sets crypt_type_id
     *
     * @param   int crypt_type_id
     * @return  int the previous value
     */
    public function setCrypt_type_id($crypt_type_id) {
      return $this->_change('crypt_type_id', $crypt_type_id);
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
     * Retrieves the Bearbeitungszustand entity
     * referenced by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bearbeitungszustand entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBz() {
      $r= XPClass::forName('de.schlund.db.methadon.Bearbeitungszustand')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Crypt_type entity
     * referenced by crypt_type_id=>crypt_type_id
     *
     * @return  de.schlund.db.methadon.Crypt_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCrypt_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Crypt_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('crypt_type_id', $this->getCrypt_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>