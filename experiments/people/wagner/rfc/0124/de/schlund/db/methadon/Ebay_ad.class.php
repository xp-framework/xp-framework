<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table ebay_ad, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ebay_ad extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..ebay_ad');
        $peer->setConnection('sybintern');
        $peer->setIdentity('ebay_ad_id');
        $peer->setPrimary(array('ebay_ad_id'));
        $peer->setTypes(array(
          'title'               => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'contact'             => array('%s', FieldType::VARCHAR, TRUE),
          'image_ext'           => array('%s', FieldType::VARCHAR, TRUE),
          'ad_type'             => array('%s', FieldType::VARCHAR, FALSE),
          'status'              => array('%s', FieldType::VARCHAR, FALSE),
          'currency'            => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'ebay_ad_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'location_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'price'               => array('%f', FieldType::MONEY, FALSE),
          'posting_date'        => array('%s', FieldType::DATETIME, FALSE),
          'expiration_date'     => array('%s', FieldType::DATETIME, FALSE),
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
     * Gets an instance of this object by index "ebay_ad_ebay_a_12807205841"
     * 
     * @param   int ebay_ad_id
     * @return  de.schlund.db.methadon.Ebay_ad entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEbay_ad_id($ebay_ad_id) {
      return new self(array(
        'ebay_ad_id'  => $ebay_ad_id,
        '_loadCrit' => new Criteria(array('ebay_ad_id', $ebay_ad_id, EQUAL))
      ));
    }

    /**
     * Retrieves title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
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
     * Retrieves contact
     *
     * @return  string
     */
    public function getContact() {
      return $this->contact;
    }
      
    /**
     * Sets contact
     *
     * @param   string contact
     * @return  string the previous value
     */
    public function setContact($contact) {
      return $this->_change('contact', $contact);
    }

    /**
     * Retrieves image_ext
     *
     * @return  string
     */
    public function getImage_ext() {
      return $this->image_ext;
    }
      
    /**
     * Sets image_ext
     *
     * @param   string image_ext
     * @return  string the previous value
     */
    public function setImage_ext($image_ext) {
      return $this->_change('image_ext', $image_ext);
    }

    /**
     * Retrieves ad_type
     *
     * @return  string
     */
    public function getAd_type() {
      return $this->ad_type;
    }
      
    /**
     * Sets ad_type
     *
     * @param   string ad_type
     * @return  string the previous value
     */
    public function setAd_type($ad_type) {
      return $this->_change('ad_type', $ad_type);
    }

    /**
     * Retrieves status
     *
     * @return  string
     */
    public function getStatus() {
      return $this->status;
    }
      
    /**
     * Sets status
     *
     * @param   string status
     * @return  string the previous value
     */
    public function setStatus($status) {
      return $this->_change('status', $status);
    }

    /**
     * Retrieves currency
     *
     * @return  string
     */
    public function getCurrency() {
      return $this->currency;
    }
      
    /**
     * Sets currency
     *
     * @param   string currency
     * @return  string the previous value
     */
    public function setCurrency($currency) {
      return $this->_change('currency', $currency);
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
     * Retrieves location_id
     *
     * @return  int
     */
    public function getLocation_id() {
      return $this->location_id;
    }
      
    /**
     * Sets location_id
     *
     * @param   int location_id
     * @return  int the previous value
     */
    public function setLocation_id($location_id) {
      return $this->_change('location_id', $location_id);
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
     * Retrieves price
     *
     * @return  float
     */
    public function getPrice() {
      return $this->price;
    }
      
    /**
     * Sets price
     *
     * @param   float price
     * @return  float the previous value
     */
    public function setPrice($price) {
      return $this->_change('price', $price);
    }

    /**
     * Retrieves posting_date
     *
     * @return  util.Date
     */
    public function getPosting_date() {
      return $this->posting_date;
    }
      
    /**
     * Sets posting_date
     *
     * @param   util.Date posting_date
     * @return  util.Date the previous value
     */
    public function setPosting_date($posting_date) {
      return $this->_change('posting_date', $posting_date);
    }

    /**
     * Retrieves expiration_date
     *
     * @return  util.Date
     */
    public function getExpiration_date() {
      return $this->expiration_date;
    }
      
    /**
     * Sets expiration_date
     *
     * @param   util.Date expiration_date
     * @return  util.Date the previous value
     */
    public function setExpiration_date($expiration_date) {
      return $this->_change('expiration_date', $expiration_date);
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
     * Retrieves the Location entity
     * referenced by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Location entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocation() {
      $r= XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Ebay_ad_category entities referencing
     * this entity by ebay_ad_id=>ebay_ad_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryEbay_adList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('ebay_ad_id', $this->getEbay_ad_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad_category entities referencing
     * this entity by ebay_ad_id=>ebay_ad_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryEbay_adIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('ebay_ad_id', $this->getEbay_ad_id(), EQUAL)
      ));
    }
  }
?>