<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table fileshare_file, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Fileshare_file extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..fileshare_file');
        $peer->setConnection('sybintern');
        $peer->setIdentity('fileshare_file_id');
        $peer->setPrimary(array('fileshare_file_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'document_name'       => array('%s', FieldType::VARCHAR, TRUE),
          'meta_data1'          => array('%s', FieldType::VARCHAR, TRUE),
          'meta_data2'          => array('%s', FieldType::VARCHAR, TRUE),
          'filename'            => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'fileshare_file_id'   => array('%d', FieldType::NUMERIC, FALSE),
          'fileshare_folder_id' => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'created'             => array('%s', FieldType::SMALLDATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_FSFILE"
     * 
     * @param   int fileshare_file_id
     * @return  de.schlund.db.methadon.Fileshare_file entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByFileshare_file_id($fileshare_file_id) {
      return new self(array(
        'fileshare_file_id'  => $fileshare_file_id,
        '_loadCrit' => new Criteria(array('fileshare_file_id', $fileshare_file_id, EQUAL))
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
     * Retrieves meta_data1
     *
     * @return  string
     */
    public function getMeta_data1() {
      return $this->meta_data1;
    }
      
    /**
     * Sets meta_data1
     *
     * @param   string meta_data1
     * @return  string the previous value
     */
    public function setMeta_data1($meta_data1) {
      return $this->_change('meta_data1', $meta_data1);
    }

    /**
     * Retrieves meta_data2
     *
     * @return  string
     */
    public function getMeta_data2() {
      return $this->meta_data2;
    }
      
    /**
     * Sets meta_data2
     *
     * @param   string meta_data2
     * @return  string the previous value
     */
    public function setMeta_data2($meta_data2) {
      return $this->_change('meta_data2', $meta_data2);
    }

    /**
     * Retrieves filename
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }
      
    /**
     * Sets filename
     *
     * @param   string filename
     * @return  string the previous value
     */
    public function setFilename($filename) {
      return $this->_change('filename', $filename);
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
     * Retrieves fileshare_file_id
     *
     * @return  int
     */
    public function getFileshare_file_id() {
      return $this->fileshare_file_id;
    }
      
    /**
     * Sets fileshare_file_id
     *
     * @param   int fileshare_file_id
     * @return  int the previous value
     */
    public function setFileshare_file_id($fileshare_file_id) {
      return $this->_change('fileshare_file_id', $fileshare_file_id);
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
     * Retrieves created
     *
     * @return  util.Date
     */
    public function getCreated() {
      return $this->created;
    }
      
    /**
     * Sets created
     *
     * @param   util.Date created
     * @return  util.Date the previous value
     */
    public function setCreated($created) {
      return $this->_change('created', $created);
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
     * Retrieves an array of all Fileshare_file_acl entities referencing
     * this entity by fileshare_file_id=>fileshare_file_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file_acl[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_aclFileshare_fileList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('fileshare_file_id', $this->getFileshare_file_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file_acl entities referencing
     * this entity by fileshare_file_id=>fileshare_file_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file_acl>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_aclFileshare_fileIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('fileshare_file_id', $this->getFileshare_file_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_file_version entities referencing
     * this entity by fileshare_file_id=>fileshare_file_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file_version[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionFileshare_fileList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('fileshare_file_id', $this->getFileshare_file_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file_version entities referencing
     * this entity by fileshare_file_id=>fileshare_file_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file_version>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionFileshare_fileIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('fileshare_file_id', $this->getFileshare_file_id(), EQUAL)
      ));
    }
  }
?>