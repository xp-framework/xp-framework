<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table mailforwarding, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Mailforwarding extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..mailforwarding');
        $peer->setConnection('sybintern');
        $peer->setIdentity('forwarding_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'source_localpart'    => array('%s', FieldType::VARCHAR, TRUE),
          'source_domainpart'   => array('%s', FieldType::VARCHAR, TRUE),
          'target_address'      => array('%s', FieldType::VARCHAR, FALSE),
          'requested_source'    => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'forwarding_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'reference_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'requester_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'request_date'        => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'feature'             => array('%d', FieldType::INTN, TRUE),
          'release_date'        => array('%s', FieldType::DATETIMN, TRUE),
          'releaser_id'         => array('%d', FieldType::NUMERICN, TRUE)
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
     * Retrieves source_localpart
     *
     * @return  string
     */
    public function getSource_localpart() {
      return $this->source_localpart;
    }
      
    /**
     * Sets source_localpart
     *
     * @param   string source_localpart
     * @return  string the previous value
     */
    public function setSource_localpart($source_localpart) {
      return $this->_change('source_localpart', $source_localpart);
    }

    /**
     * Retrieves source_domainpart
     *
     * @return  string
     */
    public function getSource_domainpart() {
      return $this->source_domainpart;
    }
      
    /**
     * Sets source_domainpart
     *
     * @param   string source_domainpart
     * @return  string the previous value
     */
    public function setSource_domainpart($source_domainpart) {
      return $this->_change('source_domainpart', $source_domainpart);
    }

    /**
     * Retrieves target_address
     *
     * @return  string
     */
    public function getTarget_address() {
      return $this->target_address;
    }
      
    /**
     * Sets target_address
     *
     * @param   string target_address
     * @return  string the previous value
     */
    public function setTarget_address($target_address) {
      return $this->_change('target_address', $target_address);
    }

    /**
     * Retrieves requested_source
     *
     * @return  string
     */
    public function getRequested_source() {
      return $this->requested_source;
    }
      
    /**
     * Sets requested_source
     *
     * @param   string requested_source
     * @return  string the previous value
     */
    public function setRequested_source($requested_source) {
      return $this->_change('requested_source', $requested_source);
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
     * Retrieves forwarding_id
     *
     * @return  int
     */
    public function getForwarding_id() {
      return $this->forwarding_id;
    }
      
    /**
     * Sets forwarding_id
     *
     * @param   int forwarding_id
     * @return  int the previous value
     */
    public function setForwarding_id($forwarding_id) {
      return $this->_change('forwarding_id', $forwarding_id);
    }

    /**
     * Retrieves reference_id
     *
     * @return  int
     */
    public function getReference_id() {
      return $this->reference_id;
    }
      
    /**
     * Sets reference_id
     *
     * @param   int reference_id
     * @return  int the previous value
     */
    public function setReference_id($reference_id) {
      return $this->_change('reference_id', $reference_id);
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
     * Retrieves requester_id
     *
     * @return  int
     */
    public function getRequester_id() {
      return $this->requester_id;
    }
      
    /**
     * Sets requester_id
     *
     * @param   int requester_id
     * @return  int the previous value
     */
    public function setRequester_id($requester_id) {
      return $this->_change('requester_id', $requester_id);
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
     * Retrieves release_date
     *
     * @return  util.Date
     */
    public function getRelease_date() {
      return $this->release_date;
    }
      
    /**
     * Sets release_date
     *
     * @param   util.Date release_date
     * @return  util.Date the previous value
     */
    public function setRelease_date($release_date) {
      return $this->_change('release_date', $release_date);
    }

    /**
     * Retrieves releaser_id
     *
     * @return  int
     */
    public function getReleaser_id() {
      return $this->releaser_id;
    }
      
    /**
     * Sets releaser_id
     *
     * @param   int releaser_id
     * @return  int the previous value
     */
    public function setReleaser_id($releaser_id) {
      return $this->_change('releaser_id', $releaser_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>releaser_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReleaser() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getReleaser_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>requester_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequester() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getRequester_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>