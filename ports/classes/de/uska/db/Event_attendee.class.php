<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 10645 2007-06-21 12:41:53Z ruben $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table event_attendee, database uska
   * (Auto-generated on Sat, 23 Jun 2007 16:52:12 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class Event_attendee extends DataSet {
    public
      $event_id           = 0,
      $player_id          = 0,
      $offers_seats       = 0,
      $needs_driver       = 0,
      $lastchange         = NULL,
      $changedby          = '',
      $attend             = 0;
  
    protected
      $cache= array(
        'Event' => array(),
        'Player' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('uska.event_attendee');
        $peer->setConnection('uska');
        $peer->setPrimary(array('event_id', 'player_id'));
        $peer->setTypes(array(
          'event_id'            => array('%d', FieldType::INT, FALSE),
          'player_id'           => array('%d', FieldType::INT, FALSE),
          'offers_seats'        => array('%d', FieldType::INT, FALSE),
          'needs_driver'        => array('%d', FieldType::INT, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'attend'              => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Event' => array(
            'classname' => 'de.uska.db.Event',
            'key'       => array(
              'event_id' => 'event_id',
            ),
          ),
          'Player' => array(
            'classname' => 'de.uska.db.Player',
            'key'       => array(
              'player_id' => 'player_id',
            ),
          ),
        ));
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
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "event_id_2"
     * 
     * @param   int event_id
     * @param   int player_id
     * @return  de.uska.db.Event_attendee entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_idPlayer_id($event_id, $player_id) {
      $r= self::getPeer()->doSelect(new Criteria(
        array('event_id', $event_id, EQUAL),
        array('player_id', $player_id, EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "event_id"
     * 
     * @param   int event_id
     * @return  de.uska.db.Event_attendee[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_id($event_id) {
      return self::getPeer()->doSelect(new Criteria(array('event_id', $event_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "player_id"
     * 
     * @param   int player_id
     * @return  de.uska.db.Event_attendee[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPlayer_id($player_id) {
      return self::getPeer()->doSelect(new Criteria(array('player_id', $player_id, EQUAL)));
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
     * Retrieves player_id
     *
     * @return  int
     */
    public function getPlayer_id() {
      return $this->player_id;
    }
      
    /**
     * Sets player_id
     *
     * @param   int player_id
     * @return  int the previous value
     */
    public function setPlayer_id($player_id) {
      return $this->_change('player_id', $player_id);
    }

    /**
     * Retrieves offers_seats
     *
     * @return  int
     */
    public function getOffers_seats() {
      return $this->offers_seats;
    }
      
    /**
     * Sets offers_seats
     *
     * @param   int offers_seats
     * @return  int the previous value
     */
    public function setOffers_seats($offers_seats) {
      return $this->_change('offers_seats', $offers_seats);
    }

    /**
     * Retrieves needs_driver
     *
     * @return  int
     */
    public function getNeeds_driver() {
      return $this->needs_driver;
    }
      
    /**
     * Sets needs_driver
     *
     * @param   int needs_driver
     * @return  int the previous value
     */
    public function setNeeds_driver($needs_driver) {
      return $this->_change('needs_driver', $needs_driver);
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
     * Retrieves attend
     *
     * @return  int
     */
    public function getAttend() {
      return $this->attend;
    }
      
    /**
     * Sets attend
     *
     * @param   int attend
     * @return  int the previous value
     */
    public function setAttend($attend) {
      return $this->_change('attend', $attend);
    }

    /**
     * Retrieves an array of all Event entities
     * referenced by event_id=>event_id
     *
     * @return  de.uska.db.Event[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventList() {
      if ($this->cached['Event']) return array_values($this->cache['Event']);
      return XPClass::forName('de.uska.db.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
        array('event_id', $this->getEvent_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event entities
     * referenced by event_id=>event_id
     *
     * @return  rdbms.ResultIterator<de.uska.db.Event
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventIterator() {
      if ($this->cached['Event']) return new HashmapIterator($this->cache['Event']);
      return XPClass::forName('de.uska.db.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
        array('event_id', $this->getEvent_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Player entities
     * referenced by player_id=>player_id
     *
     * @return  de.uska.db.Player[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlayerList() {
      if ($this->cached['Player']) return array_values($this->cache['Player']);
      return XPClass::forName('de.uska.db.Player')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
        array('player_id', $this->getPlayer_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Player entities
     * referenced by player_id=>player_id
     *
     * @return  rdbms.ResultIterator<de.uska.db.Player
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlayerIterator() {
      if ($this->cached['Player']) return new HashmapIterator($this->cache['Player']);
      return XPClass::forName('de.uska.db.Player')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
        array('player_id', $this->getPlayer_id(), EQUAL)
      ));
    }
  }
?>
