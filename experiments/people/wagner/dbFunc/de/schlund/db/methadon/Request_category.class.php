<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table request_category, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Request_category extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..request_category');
        $peer->setConnection('sybintern');
        $peer->setIdentity('request_category_id');
        $peer->setPrimary(array('request_category_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'request_category_id' => array('%d', FieldType::NUMERIC, FALSE),
          'loc_id'              => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "request_ca_reques_1552721553"
     * 
     * @param   int request_category_id
     * @return  de.schlund.db.methadon.Request_category entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRequest_category_id($request_category_id) {
      return new self(array(
        'request_category_id'  => $request_category_id,
        '_loadCrit' => new Criteria(array('request_category_id', $request_category_id, EQUAL))
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
     * Retrieves loc_id
     *
     * @return  int
     */
    public function getLoc_id() {
      return $this->loc_id;
    }
      
    /**
     * Sets loc_id
     *
     * @param   int loc_id
     * @return  int the previous value
     */
    public function setLoc_id($loc_id) {
      return $this->_change('loc_id', $loc_id);
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
     * Retrieves the Location entity
     * referenced by location_id=>loc_id
     *
     * @return  de.schlund.db.methadon.Location entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLoc() {
      $r= XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLoc_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Person_request_category entities referencing
     * this entity by request_category_id=>request_category_id
     *
     * @return  de.schlund.db.methadon.Person_request_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryRequest_categoryList() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('request_category_id', $this->getRequest_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_request_category entities referencing
     * this entity by request_category_id=>request_category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_request_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryRequest_categoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('request_category_id', $this->getRequest_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Requested_item entities referencing
     * this entity by request_category_id=>request_category_id
     *
     * @return  de.schlund.db.methadon.Requested_item[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemRequest_categoryList() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('request_category_id', $this->getRequest_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Requested_item entities referencing
     * this entity by request_category_id=>request_category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Requested_item>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemRequest_categoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('request_category_id', $this->getRequest_category_id(), EQUAL)
      ));
    }
  }
?>