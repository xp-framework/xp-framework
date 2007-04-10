<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table fileshare_folder, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Fileshare_folder extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..fileshare_folder');
        $peer->setConnection('sybintern');
        $peer->setIdentity('fileshare_folder_id');
        $peer->setPrimary(array('fileshare_folder_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'document_name'       => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'fileshare_folder_id' => array('%d', FieldType::NUMERIC, FALSE),
          'fileshare_folder_type_id' => array('%d', FieldType::NUMERIC, FALSE),
          'parent_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_FS_FOLDER"
     * 
     * @param   int fileshare_folder_id
     * @return  de.schlund.db.methadon.Fileshare_folder entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByFileshare_folder_id($fileshare_folder_id) {
      return new self(array(
        'fileshare_folder_id'  => $fileshare_folder_id,
        '_loadCrit' => new Criteria(array('fileshare_folder_id', $fileshare_folder_id, EQUAL))
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
     * Retrieves document_name
     *
     * @return  string
     */
    public function getDocument_name() {
      return $this->document_name;
    }
      
    /**
     * Sets document_name
     *
     * @param   string document_name
     * @return  string the previous value
     */
    public function setDocument_name($document_name) {
      return $this->_change('document_name', $document_name);
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
     * Retrieves fileshare_folder_type_id
     *
     * @return  int
     */
    public function getFileshare_folder_type_id() {
      return $this->fileshare_folder_type_id;
    }
      
    /**
     * Sets fileshare_folder_type_id
     *
     * @param   int fileshare_folder_type_id
     * @return  int the previous value
     */
    public function setFileshare_folder_type_id($fileshare_folder_type_id) {
      return $this->_change('fileshare_folder_type_id', $fileshare_folder_type_id);
    }

    /**
     * Retrieves parent_id
     *
     * @return  int
     */
    public function getParent_id() {
      return $this->parent_id;
    }
      
    /**
     * Sets parent_id
     *
     * @param   int parent_id
     * @return  int the previous value
     */
    public function setParent_id($parent_id) {
      return $this->_change('parent_id', $parent_id);
    }

    /**
     * Retrieves owner_id
     *
     * @return  int
     */
    public function getOwner_id() {
      return $this->owner_id;
    }
      
    /**
     * Sets owner_id
     *
     * @param   int owner_id
     * @return  int the previous value
     */
    public function setOwner_id($owner_id) {
      return $this->_change('owner_id', $owner_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>owner_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOwner() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getOwner_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves an array of all References entities
     * referenced by fileshare_folder_id=>parent_id
     *
     * @return  de.schlund.db.methadon.References[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getParentList() {
      return XPClass::forName('de.schlund.db.methadon.References')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
        array('fileshare_folder_id', $this->getParent_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all References entities
     * referenced by fileshare_folder_id=>parent_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.References
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getParentIterator() {
      return XPClass::forName('de.schlund.db.methadon.References')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
        array('fileshare_folder_id', $this->getParent_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_folder_acl entities referencing
     * this entity by fileshare_folder_id=>fileshare_folder_id
     *
     * @return  de.schlund.db.methadon.Fileshare_folder_acl[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folder_aclFileshare_folderList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('fileshare_folder_id', $this->getFileshare_folder_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_folder_acl entities referencing
     * this entity by fileshare_folder_id=>fileshare_folder_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_folder_acl>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folder_aclFileshare_folderIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('fileshare_folder_id', $this->getFileshare_folder_id(), EQUAL)
      ));
    }
  }
?>