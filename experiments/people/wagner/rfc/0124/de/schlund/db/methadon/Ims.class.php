<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table ims, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ims extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..ims');
        $peer->setConnection('sybintern');
        $peer->setIdentity('ims_id');
        $peer->setPrimary(array('ims_id'));
        $peer->setTypes(array(
          'template'            => array('%s', FieldType::VARCHAR, FALSE),
          'output_type'         => array('%s', FieldType::VARCHAR, FALSE),
          'reference'           => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'ims_id'              => array('%d', FieldType::NUMERIC, FALSE),
          'sender_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'recipient_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'feature'             => array('%d', FieldType::NUMERIC, FALSE),
          'valid_from'          => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'message'             => array('%s', FieldType::TEXT, FALSE)
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
     * Gets an instance of this object by index "ims_ims_id_10247196721"
     * 
     * @param   int ims_id
     * @return  de.schlund.db.methadon.Ims entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByIms_id($ims_id) {
      return new self(array(
        'ims_id'  => $ims_id,
        '_loadCrit' => new Criteria(array('ims_id', $ims_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "ims_001"
     * 
     * @param   int bz_id
     * @return  de.schlund.db.methadon.Ims[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('bz_id', $bz_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves template
     *
     * @return  string
     */
    public function getTemplate() {
      return $this->template;
    }
      
    /**
     * Sets template
     *
     * @param   string template
     * @return  string the previous value
     */
    public function setTemplate($template) {
      return $this->_change('template', $template);
    }

    /**
     * Retrieves output_type
     *
     * @return  string
     */
    public function getOutput_type() {
      return $this->output_type;
    }
      
    /**
     * Sets output_type
     *
     * @param   string output_type
     * @return  string the previous value
     */
    public function setOutput_type($output_type) {
      return $this->_change('output_type', $output_type);
    }

    /**
     * Retrieves reference
     *
     * @return  string
     */
    public function getReference() {
      return $this->reference;
    }
      
    /**
     * Sets reference
     *
     * @param   string reference
     * @return  string the previous value
     */
    public function setReference($reference) {
      return $this->_change('reference', $reference);
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
     * Retrieves ims_id
     *
     * @return  int
     */
    public function getIms_id() {
      return $this->ims_id;
    }
      
    /**
     * Sets ims_id
     *
     * @param   int ims_id
     * @return  int the previous value
     */
    public function setIms_id($ims_id) {
      return $this->_change('ims_id', $ims_id);
    }

    /**
     * Retrieves sender_id
     *
     * @return  int
     */
    public function getSender_id() {
      return $this->sender_id;
    }
      
    /**
     * Sets sender_id
     *
     * @param   int sender_id
     * @return  int the previous value
     */
    public function setSender_id($sender_id) {
      return $this->_change('sender_id', $sender_id);
    }

    /**
     * Retrieves recipient_id
     *
     * @return  int
     */
    public function getRecipient_id() {
      return $this->recipient_id;
    }
      
    /**
     * Sets recipient_id
     *
     * @param   int recipient_id
     * @return  int the previous value
     */
    public function setRecipient_id($recipient_id) {
      return $this->_change('recipient_id', $recipient_id);
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
     * Retrieves feature
     *
     * @return  int
     */
    public function getFeature() {
      return $this->feature;
    }
      
    /**
     * Sets feature
     *
     * @param   int feature
     * @return  int the previous value
     */
    public function setFeature($feature) {
      return $this->_change('feature', $feature);
    }

    /**
     * Retrieves valid_from
     *
     * @return  util.Date
     */
    public function getValid_from() {
      return $this->valid_from;
    }
      
    /**
     * Sets valid_from
     *
     * @param   util.Date valid_from
     * @return  util.Date the previous value
     */
    public function setValid_from($valid_from) {
      return $this->_change('valid_from', $valid_from);
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
     * Retrieves message
     *
     * @return  string
     */
    public function getMessage() {
      return $this->message;
    }
      
    /**
     * Sets message
     *
     * @param   string message
     * @return  string the previous value
     */
    public function setMessage($message) {
      return $this->_change('message', $message);
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
     * referenced by person_id=>sender_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getSender() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getSender_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>recipient_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRecipient() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getRecipient_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>