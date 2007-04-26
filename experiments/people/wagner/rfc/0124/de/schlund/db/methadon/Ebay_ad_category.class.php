<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table ebay_ad_category, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ebay_ad_category extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..ebay_ad_category');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'ebay_ad_id'          => array('%d', FieldType::NUMERIC, FALSE),
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
     * Retrieves ebay_ad_id
     *
     * @return  int
     */
    public function getEbay_ad_id() {
      return $this->ebay_ad_id;
    }
      
    /**
     * Sets ebay_ad_id
     *
     * @param   int ebay_ad_id
     * @return  int the previous value
     */
    public function setEbay_ad_id($ebay_ad_id) {
      return $this->_change('ebay_ad_id', $ebay_ad_id);
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
     * Retrieves the Ebay_ad entity
     * referenced by ebay_ad_id=>ebay_ad_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad() {
      $r= XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('ebay_ad_id', $this->getEbay_ad_id(), EQUAL)
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
     * Retrieves the Ebay_category entity
     * referenced by ebay_category_id=>ebay_category_id
     *
     * @return  de.schlund.db.methadon.Ebay_category entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_category() {
      $r= XPClass::forName('de.schlund.db.methadon.Ebay_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('ebay_category_id', $this->getEbay_category_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>