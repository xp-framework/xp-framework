<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table event_template_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Event_template_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..event_template_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'event_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'event_template_id'   => array('%d', FieldType::NUMERIC, FALSE),
          'reference_date'      => array('%s', FieldType::DATETIME, FALSE)
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
     * Retrieves event_id
     *
     * @return  int
     */
    public function getEvent_id() {
      return $this->event_id;
    }
      
    /**
     * Sets event_id
     *
     * @param   int event_id
     * @return  int the previous value
     */
    public function setEvent_id($event_id) {
      return $this->_change('event_id', $event_id);
    }

    /**
     * Retrieves event_template_id
     *
     * @return  int
     */
    public function getEvent_template_id() {
      return $this->event_template_id;
    }
      
    /**
     * Sets event_template_id
     *
     * @param   int event_template_id
     * @return  int the previous value
     */
    public function setEvent_template_id($event_template_id) {
      return $this->_change('event_template_id', $event_template_id);
    }

    /**
     * Retrieves reference_date
     *
     * @return  util.Date
     */
    public function getReference_date() {
      return $this->reference_date;
    }
      
    /**
     * Sets reference_date
     *
     * @param   util.Date reference_date
     * @return  util.Date the previous value
     */
    public function setReference_date($reference_date) {
      return $this->_change('reference_date', $reference_date);
    }

    /**
     * Retrieves the Event entity
     * referenced by event_id=>event_id
     *
     * @return  de.schlund.db.methadon.Event entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent() {
      $r= XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_id', $this->getEvent_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Event_template entity
     * referenced by event_template_id=>event_template_id
     *
     * @return  de.schlund.db.methadon.Event_template entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_template() {
      $r= XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_template_id', $this->getEvent_template_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>