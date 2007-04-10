<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table fileshare_folder_acl, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Fileshare_folder_acl extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..fileshare_folder_acl');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('fileshare_folder_id', 'person_id'));
        $peer->setTypes(array(
          'permission'          => array('%d', FieldType::INT, FALSE),
          'fileshare_folder_id' => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_FSFOLDER_ACL"
     * 
     * @param   int fileshare_folder_id
     * @param   int person_id
     * @return  de.schlund.db.methadon.Fileshare_folder_acl entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByFileshare_folder_idPerson_id($fileshare_folder_id, $person_id) {
      return new self(array(
        'fileshare_folder_id'  => $fileshare_folder_id,
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(
          array('fileshare_folder_id', $fileshare_folder_id, EQUAL),
          array('person_id', $person_id, EQUAL)
        )
      ));
    }

    /**
     * Retrieves permission
     *
     * @return  int
     */
    public function getPermission() {
      return $this->permission;
    }
      
    /**
     * Sets permission
     *
     * @param   int permission
     * @return  int the previous value
     */
    public function setPermission($permission) {
      return $this->_change('permission', $permission);
    }

    /**
     * Retrieves fileshare_folder_id
     *
     * @return  int
     */
    public function getFileshare_folder_id() {
      return $this->fileshare_folder_id;
    }
      
    /**
     * Sets fileshare_folder_id
     *
     * @param   int fileshare_folder_id
     * @return  int the previous value
     */
    public function setFileshare_folder_id($fileshare_folder_id) {
      return $this->_change('fileshare_folder_id', $fileshare_folder_id);
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

    /**
     * Retrieves the Fileshare_folder entity
     * referenced by fileshare_folder_id=>fileshare_folder_id
     *
     * @return  de.schlund.db.methadon.Fileshare_folder entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folder() {
      $r= XPClass::forName('de.schlund.db.methadon.Fileshare_folder')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('fileshare_folder_id', $this->getFileshare_folder_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>