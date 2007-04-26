<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table reservation_history, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Reservation_history extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..reservation_history');
        $peer->setConnection('sybintern');
        $peer->setIdentity('reservation_history_id');
        $peer->setPrimary(array('reservation_history_id'));
        $peer->setTypes(array(
          'reservation_history_id' => array('%d', FieldType::NUMERIC, FALSE),
          'resource_reservation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'start_date'          => array('%s', FieldType::DATETIME, FALSE),
          'end_date'            => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "reservatio_reserv_1549245543"
     * 
     * @param   int reservation_history_id
     * @return  de.schlund.db.methadon.Reservation_history entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByReservation_history_id($reservation_history_id) {
      return new self(array(
        'reservation_history_id'  => $reservation_history_id,
        '_loadCrit' => new Criteria(array('reservation_history_id', $reservation_history_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_reservation_history_table"
     * 
     * @param   int resource_reservation_id
     * @return  de.schlund.db.methadon.Reservation_history[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_reservation_id($resource_reservation_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('resource_reservation_id', $resource_reservation_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves reservation_history_id
     *
     * @return  int
     */
    public function getReservation_history_id() {
      return $this->reservation_history_id;
    }
      
    /**
     * Sets reservation_history_id
     *
     * @param   int reservation_history_id
     * @return  int the previous value
     */
    public function setReservation_history_id($reservation_history_id) {
      return $this->_change('reservation_history_id', $reservation_history_id);
    }

    /**
     * Retrieves resource_reservation_id
     *
     * @return  int
     */
    public function getResource_reservation_id() {
      return $this->resource_reservation_id;
    }
      
    /**
     * Sets resource_reservation_id
     *
     * @param   int resource_reservation_id
     * @return  int the previous value
     */
    public function setResource_reservation_id($resource_reservation_id) {
      return $this->_change('resource_reservation_id', $resource_reservation_id);
    }

    /**
     * Retrieves start_date
     *
     * @return  util.Date
     */
    public function getStart_date() {
      return $this->start_date;
    }
      
    /**
     * Sets start_date
     *
     * @param   util.Date start_date
     * @return  util.Date the previous value
     */
    public function setStart_date($start_date) {
      return $this->_change('start_date', $start_date);
    }

    /**
     * Retrieves end_date
     *
     * @return  util.Date
     */
    public function getEnd_date() {
      return $this->end_date;
    }
      
    /**
     * Sets end_date
     *
     * @param   util.Date end_date
     * @return  util.Date the previous value
     */
    public function setEnd_date($end_date) {
      return $this->_change('end_date', $end_date);
    }

    /**
     * Retrieves the Resource_reservation entity
     * referenced by resource_reservation_id=>resource_reservation_id
     *
     * @return  de.schlund.db.methadon.Resource_reservation entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservation() {
      $r= XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>