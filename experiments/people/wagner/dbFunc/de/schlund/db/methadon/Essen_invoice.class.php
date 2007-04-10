<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table essen_invoice, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Essen_invoice extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..essen_invoice');
        $peer->setConnection('sybintern');
        $peer->setIdentity('invoice_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'value'               => array('%d', FieldType::INT, FALSE),
          'invoice_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'currency_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'date_from'           => array('%s', FieldType::SMALLDATETIME, FALSE),
          'date_to'             => array('%s', FieldType::SMALLDATETIME, FALSE)
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
     * Retrieves date_from
     *
     * @return  util.Date
     */
    public function getDate_from() {
      return $this->date_from;
    }
      
    /**
     * Sets date_from
     *
     * @param   util.Date date_from
     * @return  util.Date the previous value
     */
    public function setDate_from($date_from) {
      return $this->_change('date_from', $date_from);
    }

    /**
     * Retrieves date_to
     *
     * @return  util.Date
     */
    public function getDate_to() {
      return $this->date_to;
    }
      
    /**
     * Sets date_to
     *
     * @param   util.Date date_to
     * @return  util.Date the previous value
     */
    public function setDate_to($date_to) {
      return $this->_change('date_to', $date_to);
    }
  }
?>