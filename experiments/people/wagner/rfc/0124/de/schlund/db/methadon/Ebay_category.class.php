<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table ebay_category, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ebay_category extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..ebay_category');
        $peer->setConnection('sybintern');
        $peer->setIdentity('ebay_category_id');
        $peer->setPrimary(array('ebay_category_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'ebay_category_id'    => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "ebay_categ_ebay_c_1184720242"
     * 
     * @param   int ebay_category_id
     * @return  de.schlund.db.methadon.Ebay_category entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEbay_category_id($ebay_category_id) {
      return new self(array(
        'ebay_category_id'  => $ebay_category_id,
        '_loadCrit' => new Criteria(array('ebay_category_id', $ebay_category_id, EQUAL))
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
     * Retrieves ebay_category_id
     *
     * @return  int
     */
    public function getEbay_category_id() {
      return $this->ebay_category_id;
    }
      
    /**
     * Sets ebay_category_id
     *
     * @param   int ebay_category_id
     * @return  int the previous value
     */
    public function setEbay_category_id($ebay_category_id) {
      return $this->_change('ebay_category_id', $ebay_category_id);
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
     * Retrieves an array of all Ebay_ad_category entities referencing
     * this entity by ebay_category_id=>ebay_category_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryEbay_categoryList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('ebay_category_id', $this->getEbay_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad_category entities referencing
     * this entity by ebay_category_id=>ebay_category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryEbay_categoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('ebay_category_id', $this->getEbay_category_id(), EQUAL)
      ));
    }
  }
?>