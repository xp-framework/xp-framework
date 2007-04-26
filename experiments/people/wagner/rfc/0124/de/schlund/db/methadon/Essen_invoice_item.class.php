<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table essen_invoice_item, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Essen_invoice_item extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..essen_invoice_item');
        $peer->setConnection('sybintern');
        $peer->setIdentity('invoice_item_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'value'               => array('%d', FieldType::INT, FALSE),
          'invoice_item_id'     => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'meal_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'offer_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'currency_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'pos'                 => array('%d', FieldType::INTN, TRUE),
          'date'                => array('%s', FieldType::SMALLDATETIME, FALSE),
          'invoice_id'          => array('%d', FieldType::NUMERICN, TRUE)
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
     * Retrieves value
     *
     * @return  int
     */
    public function getValue() {
      return $this->value;
    }
      
    /**
     * Sets value
     *
     * @param   int value
     * @return  int the previous value
     */
    public function setValue($value) {
      return $this->_change('value', $value);
    }

    /**
     * Retrieves invoice_item_id
     *
     * @return  int
     */
    public function getInvoice_item_id() {
      return $this->invoice_item_id;
    }
      
    /**
     * Sets invoice_item_id
     *
     * @param   int invoice_item_id
     * @return  int the previous value
     */
    public function setInvoice_item_id($invoice_item_id) {
      return $this->_change('invoice_item_id', $invoice_item_id);
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
     * Retrieves meal_id
     *
     * @return  int
     */
    public function getMeal_id() {
      return $this->meal_id;
    }
      
    /**
     * Sets meal_id
     *
     * @param   int meal_id
     * @return  int the previous value
     */
    public function setMeal_id($meal_id) {
      return $this->_change('meal_id', $meal_id);
    }

    /**
     * Retrieves offer_id
     *
     * @return  int
     */
    public function getOffer_id() {
      return $this->offer_id;
    }
      
    /**
     * Sets offer_id
     *
     * @param   int offer_id
     * @return  int the previous value
     */
    public function setOffer_id($offer_id) {
      return $this->_change('offer_id', $offer_id);
    }

    /**
     * Retrieves currency_id
     *
     * @return  int
     */
    public function getCurrency_id() {
      return $this->currency_id;
    }
      
    /**
     * Sets currency_id
     *
     * @param   int currency_id
     * @return  int the previous value
     */
    public function setCurrency_id($currency_id) {
      return $this->_change('currency_id', $currency_id);
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
     * Retrieves pos
     *
     * @return  int
     */
    public function getPos() {
      return $this->pos;
    }
      
    /**
     * Sets pos
     *
     * @param   int pos
     * @return  int the previous value
     */
    public function setPos($pos) {
      return $this->_change('pos', $pos);
    }

    /**
     * Retrieves date
     *
     * @return  util.Date
     */
    public function getDate() {
      return $this->date;
    }
      
    /**
     * Sets date
     *
     * @param   util.Date date
     * @return  util.Date the previous value
     */
    public function setDate($date) {
      return $this->_change('date', $date);
    }

    /**
     * Retrieves invoice_id
     *
     * @return  int
     */
    public function getInvoice_id() {
      return $this->invoice_id;
    }
      
    /**
     * Sets invoice_id
     *
     * @param   int invoice_id
     * @return  int the previous value
     */
    public function setInvoice_id($invoice_id) {
      return $this->_change('invoice_id', $invoice_id);
    }
  }
?>