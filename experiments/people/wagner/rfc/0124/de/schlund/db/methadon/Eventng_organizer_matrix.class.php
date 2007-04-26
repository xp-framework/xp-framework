<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table eventng_organizer_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Eventng_organizer_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..eventng_organizer_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'eventng_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'organizer_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'organizer_type'      => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves eventng_id
     *
     * @return  int
     */
    public function getEventng_id() {
      return $this->eventng_id;
    }
      
    /**
     * Sets eventng_id
     *
     * @param   int eventng_id
     * @return  int the previous value
     */
    public function setEventng_id($eventng_id) {
      return $this->_change('eventng_id', $eventng_id);
    }

    /**
     * Retrieves organizer_id
     *
     * @return  int
     */
    public function getOrganizer_id() {
      return $this->organizer_id;
    }
      
    /**
     * Sets organizer_id
     *
     * @param   int organizer_id
     * @return  int the previous value
     */
    public function setOrganizer_id($organizer_id) {
      return $this->_change('organizer_id', $organizer_id);
    }

    /**
     * Retrieves organizer_type
     *
     * @return  int
     */
    public function getOrganizer_type() {
      return $this->organizer_type;
    }
      
    /**
     * Sets organizer_type
     *
     * @param   int organizer_type
     * @return  int the previous value
     */
    public function setOrganizer_type($organizer_type) {
      return $this->_change('organizer_type', $organizer_type);
    }

    /**
     * Retrieves the Eventng entity
     * referenced by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng() {
      $r= XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>organizer_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOrganizer() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getOrganizer_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>