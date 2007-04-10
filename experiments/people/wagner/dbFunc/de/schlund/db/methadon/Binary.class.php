<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table binary, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Binary extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..binary');
        $peer->setConnection('sybintern');
        $peer->setIdentity('binary_id');
        $peer->setPrimary(array('binary_id'));
        $peer->setTypes(array(
          'filename'            => array('%s', FieldType::VARCHAR, FALSE),
          'content_type'        => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'secret'              => array('%s', FieldType::VARCHAR, TRUE),
          'binary_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'owner'               => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_binary"
     * 
     * @param   int binary_id
     * @return  de.schlund.db.methadon.Binary entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBinary_id($binary_id) {
      return new self(array(
        'binary_id'  => $binary_id,
        '_loadCrit' => new Criteria(array('binary_id', $binary_id, EQUAL))
      ));
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
     * Retrieves secret
     *
     * @return  string
     */
    public function getSecret() {
      return $this->secret;
    }
      
    /**
     * Sets secret
     *
     * @param   string secret
     * @return  string the previous value
     */
    public function setSecret($secret) {
      return $this->_change('secret', $secret);
    }

    /**
     * Retrieves binary_id
     *
     * @return  int
     */
    public function getBinary_id() {
      return $this->binary_id;
    }
      
    /**
     * Sets binary_id
     *
     * @param   int binary_id
     * @return  int the previous value
     */
    public function setBinary_id($binary_id) {
      return $this->_change('binary_id', $binary_id);
    }

    /**
     * Retrieves owner
     *
     * @return  int
     */
    public function getOwner() {
      return $this->owner;
    }
      
    /**
     * Sets owner
     *
     * @param   int owner
     * @return  int the previous value
     */
    public function setOwner($owner) {
      return $this->_change('owner', $owner);
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
     * Retrieves an array of all Bug_binaryhistory_matrix entities referencing
     * this entity by binary_id=>binary_id
     *
     * @return  de.schlund.db.methadon.Bug_binaryhistory_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_binaryhistory_matrixBinaryList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_binaryhistory_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('binary_id', $this->getBinary_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_binaryhistory_matrix entities referencing
     * this entity by binary_id=>binary_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_binaryhistory_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_binaryhistory_matrixBinaryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_binaryhistory_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('binary_id', $this->getBinary_id(), EQUAL)
      ));
    }
  }
?>