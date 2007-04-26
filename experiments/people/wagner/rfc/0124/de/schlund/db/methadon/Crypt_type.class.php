<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table crypt_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Crypt_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..crypt_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('crypt_type_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'crypt_type_id'       => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_CRYPTTYPE"
     * 
     * @param   int crypt_type_id
     * @return  de.schlund.db.methadon.Crypt_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCrypt_type_id($crypt_type_id) {
      return new self(array(
        'crypt_type_id'  => $crypt_type_id,
        '_loadCrit' => new Criteria(array('crypt_type_id', $crypt_type_id, EQUAL))
      ));
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
     * Retrieves an array of all External_password entities referencing
     * this entity by crypt_type_id=>crypt_type_id
     *
     * @return  de.schlund.db.methadon.External_password[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_passwordCrypt_typeList() {
      return XPClass::forName('de.schlund.db.methadon.External_password')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('crypt_type_id', $this->getCrypt_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all External_password entities referencing
     * this entity by crypt_type_id=>crypt_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.External_password>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_passwordCrypt_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.External_password')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('crypt_type_id', $this->getCrypt_type_id(), EQUAL)
      ));
    }
  }
?>