<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table eventng_reminder, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Eventng_reminder extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..eventng_reminder');
        $peer->setConnection('sybintern');
        $peer->setIdentity('eventng_reminder_id');
        $peer->setPrimary(array('eventng_reminder_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'minutes'             => array('%d', FieldType::INT, FALSE),
          'eventng_reminder_id' => array('%d', FieldType::NUMERIC, FALSE),
          'eventng_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'apparition'          => array('%d', FieldType::INTN, TRUE)
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
     * Gets an instance of this object by index "eventng_re_eventn_8257669681"
     * 
     * @param   int eventng_reminder_id
     * @return  de.schlund.db.methadon.Eventng_reminder entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEventng_reminder_id($eventng_reminder_id) {
      return new self(array(
        'eventng_reminder_id'  => $eventng_reminder_id,
        '_loadCrit' => new Criteria(array('eventng_reminder_id', $eventng_reminder_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_eventng_reminder_table"
     * 
     * @param   int eventng_id
     * @return  de.schlund.db.methadon.Eventng_reminder[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEventng_id($eventng_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('eventng_id', $eventng_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves minutes
     *
     * @return  int
     */
    public function getMinutes() {
      return $this->minutes;
    }
      
    /**
     * Sets minutes
     *
     * @param   int minutes
     * @return  int the previous value
     */
    public function setMinutes($minutes) {
      return $this->_change('minutes', $minutes);
    }

    /**
     * Retrieves eventng_reminder_id
     *
     * @return  int
     */
    public function getEventng_reminder_id() {
      return $this->eventng_reminder_id;
    }
      
    /**
     * Sets eventng_reminder_id
     *
     * @param   int eventng_reminder_id
     * @return  int the previous value
     */
    public function setEventng_reminder_id($eventng_reminder_id) {
      return $this->_change('eventng_reminder_id', $eventng_reminder_id);
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
     * Retrieves apparition
     *
     * @return  int
     */
    public function getApparition() {
      return $this->apparition;
    }
      
    /**
     * Sets apparition
     *
     * @param   int apparition
     * @return  int the previous value
     */
    public function setApparition($apparition) {
      return $this->_change('apparition', $apparition);
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
  }
?>