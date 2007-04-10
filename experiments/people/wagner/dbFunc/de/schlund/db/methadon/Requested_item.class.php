<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table requested_item, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Requested_item extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..requested_item');
        $peer->setConnection('sybintern');
        $peer->setIdentity('requested_item_id');
        $peer->setPrimary(array('requested_item_id'));
        $peer->setTypes(array(
          'reason'              => array('%s', FieldType::VARCHAR, TRUE),
          'supplier'            => array('%s', FieldType::VARCHAR, TRUE),
          'price'               => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'request_status'      => array('%d', FieldType::SMALLINT, FALSE),
          'requested_item_id'   => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'master_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'request_category_id' => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'request_date'        => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'designation'         => array('%s', FieldType::TEXT, TRUE)
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
     * Gets an instance of this object by index "requested__reques_1792722408"
     * 
     * @param   int requested_item_id
     * @return  de.schlund.db.methadon.Requested_item entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRequested_item_id($requested_item_id) {
      return new self(array(
        'requested_item_id'  => $requested_item_id,
        '_loadCrit' => new Criteria(array('requested_item_id', $requested_item_id, EQUAL))
      ));
    }

    /**
     * Retrieves reason
     *
     * @return  string
     */
    public function getReason() {
      return $this->reason;
    }
      
    /**
     * Sets reason
     *
     * @param   string reason
     * @return  string the previous value
     */
    public function setReason($reason) {
      return $this->_change('reason', $reason);
    }

    /**
     * Retrieves supplier
     *
     * @return  string
     */
    public function getSupplier() {
      return $this->supplier;
    }
      
    /**
     * Sets supplier
     *
     * @param   string supplier
     * @return  string the previous value
     */
    public function setSupplier($supplier) {
      return $this->_change('supplier', $supplier);
    }

    /**
     * Retrieves price
     *
     * @return  string
     */
    public function getPrice() {
      return $this->price;
    }
      
    /**
     * Sets price
     *
     * @param   string price
     * @return  string the previous value
     */
    public function setPrice($price) {
      return $this->_change('price', $price);
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
     * Retrieves request_status
     *
     * @return  int
     */
    public function getRequest_status() {
      return $this->request_status;
    }
      
    /**
     * Sets request_status
     *
     * @param   int request_status
     * @return  int the previous value
     */
    public function setRequest_status($request_status) {
      return $this->_change('request_status', $request_status);
    }

    /**
     * Retrieves requested_item_id
     *
     * @return  int
     */
    public function getRequested_item_id() {
      return $this->requested_item_id;
    }
      
    /**
     * Sets requested_item_id
     *
     * @param   int requested_item_id
     * @return  int the previous value
     */
    public function setRequested_item_id($requested_item_id) {
      return $this->_change('requested_item_id', $requested_item_id);
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
     * Retrieves master_id
     *
     * @return  int
     */
    public function getMaster_id() {
      return $this->master_id;
    }
      
    /**
     * Sets master_id
     *
     * @param   int master_id
     * @return  int the previous value
     */
    public function setMaster_id($master_id) {
      return $this->_change('master_id', $master_id);
    }

    /**
     * Retrieves request_category_id
     *
     * @return  int
     */
    public function getRequest_category_id() {
      return $this->request_category_id;
    }
      
    /**
     * Sets request_category_id
     *
     * @param   int request_category_id
     * @return  int the previous value
     */
    public function setRequest_category_id($request_category_id) {
      return $this->_change('request_category_id', $request_category_id);
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
     * Retrieves request_date
     *
     * @return  util.Date
     */
    public function getRequest_date() {
      return $this->request_date;
    }
      
    /**
     * Sets request_date
     *
     * @param   util.Date request_date
     * @return  util.Date the previous value
     */
    public function setRequest_date($request_date) {
      return $this->_change('request_date', $request_date);
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
     * Retrieves designation
     *
     * @return  string
     */
    public function getDesignation() {
      return $this->designation;
    }
      
    /**
     * Sets designation
     *
     * @param   string designation
     * @return  string the previous value
     */
    public function setDesignation($designation) {
      return $this->_change('designation', $designation);
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
     * Retrieves the Person entity
     * referenced by person_id=>master_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaster() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getMaster_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves the Request_category entity
     * referenced by request_category_id=>request_category_id
     *
     * @return  de.schlund.db.methadon.Request_category entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequest_category() {
      $r= XPClass::forName('de.schlund.db.methadon.Request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('request_category_id', $this->getRequest_category_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>