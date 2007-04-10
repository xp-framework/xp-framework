<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table reservation_exception, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Reservation_exception extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..reservation_exception');
        $peer->setConnection('sybintern');
        $peer->setIdentity('reservation_exception_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'reservation_exception_id' => array('%d', FieldType::NUMERIC, FALSE),
          'resource_reservation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'exception_date'      => array('%s', FieldType::DATETIME, FALSE)
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
     * Retrieves reservation_exception_id
     *
     * @return  int
     */
    public function getReservation_exception_id() {
      return $this->reservation_exception_id;
    }
      
    /**
     * Sets reservation_exception_id
     *
     * @param   int reservation_exception_id
     * @return  int the previous value
     */
    public function setReservation_exception_id($reservation_exception_id) {
      return $this->_change('reservation_exception_id', $reservation_exception_id);
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
     * Retrieves exception_date
     *
     * @return  util.Date
     */
    public function getException_date() {
      return $this->exception_date;
    }
      
    /**
     * Sets exception_date
     *
     * @param   util.Date exception_date
     * @return  util.Date the previous value
     */
    public function setException_date($exception_date) {
      return $this->_change('exception_date', $exception_date);
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