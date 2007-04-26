<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table fileshare_file_version, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Fileshare_file_version extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..fileshare_file_version');
        $peer->setConnection('sybintern');
        $peer->setIdentity('fileshare_file_version_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'content_type'        => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'filesize'            => array('%d', FieldType::INT, FALSE),
          'version_number'      => array('%f', FieldType::FLOAT, FALSE),
          'fileshare_file_version_id' => array('%d', FieldType::NUMERIC, FALSE),
          'fileshare_file_id'   => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
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
     * Retrieves content_type
     *
     * @return  string
     */
    public function getContent_type() {
      return $this->content_type;
    }
      
    /**
     * Sets content_type
     *
     * @param   string content_type
     * @return  string the previous value
     */
    public function setContent_type($content_type) {
      return $this->_change('content_type', $content_type);
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
     * Retrieves filesize
     *
     * @return  int
     */
    public function getFilesize() {
      return $this->filesize;
    }
      
    /**
     * Sets filesize
     *
     * @param   int filesize
     * @return  int the previous value
     */
    public function setFilesize($filesize) {
      return $this->_change('filesize', $filesize);
    }

    /**
     * Retrieves version_number
     *
     * @return  float
     */
    public function getVersion_number() {
      return $this->version_number;
    }
      
    /**
     * Sets version_number
     *
     * @param   float version_number
     * @return  float the previous value
     */
    public function setVersion_number($version_number) {
      return $this->_change('version_number', $version_number);
    }

    /**
     * Retrieves fileshare_file_version_id
     *
     * @return  int
     */
    public function getFileshare_file_version_id() {
      return $this->fileshare_file_version_id;
    }
      
    /**
     * Sets fileshare_file_version_id
     *
     * @param   int fileshare_file_version_id
     * @return  int the previous value
     */
    public function setFileshare_file_version_id($fileshare_file_version_id) {
      return $this->_change('fileshare_file_version_id', $fileshare_file_version_id);
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
     * Retrieves the Fileshare_file entity
     * referenced by fileshare_file_id=>fileshare_file_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file() {
      $r= XPClass::forName('de.schlund.db.methadon.Fileshare_file')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('fileshare_file_id', $this->getFileshare_file_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>