<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table contributor, database CAFFEINE
   * (This class was auto-generated, so please do not change manually)
   *
   * @purpose  Datasource accessor
   */
  class Contributor extends DataSet {
    public
      $rfc_id             = 0,
      $person_id          = 0;
  
    protected
      $cache= array(
        'Rfc' => array(),
        'Person' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('CAFFEINE.contributor');
        $peer->setConnection('caffeine');
        $peer->setPrimary(array('rfc_id', 'person_id'));
        $peer->setTypes(array(
          'rfc_id'              => array('%d', FieldType::INT, FALSE),
          'person_id'           => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Rfc' => array(
            'classname' => 'net.xp_framework.db.caffeine.Rfc',
            'key'       => array(
              'rfc_id' => 'rfc_id',
            ),
          ),
          'Person' => array(
            'classname' => 'net.xp_framework.db.caffeine.Person',
            'key'       => array(
              'person_id' => 'person_id',
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
     * @throws  lang.IllegalArgumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int rfc_id
     * @param   int person_id
     * @return  net.xp_framework.db.caffeine.Contributor entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRfc_idPerson_id($rfc_id, $person_id) {
      $r= self::getPeer()->doSelect(new Criteria(
        array('rfc_id', $rfc_id, EQUAL),
        array('person_id', $person_id, EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "contributor_Fperson"
     * 
     * @param   int person_id
     * @return  net.xp_framework.db.caffeine.Contributor[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
    }

    /**
     * Retrieves rfc_id
     *
     * @return  int
     */
    public function getRfc_id() {
      return $this->rfc_id;
    }
      
    /**
     * Sets rfc_id
     *
     * @param   int rfc_id
     * @return  int the previous value
     */
    public function setRfc_id($rfc_id) {
      return $this->_change('rfc_id', $rfc_id);
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
     * Retrieves the Rfc entity
     * referenced by rfc_id=>rfc_id
     *
     * @return  net.xp_framework.db.caffeine.Rfc entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRfc() {
      $r= ($this->cached['Rfc']) ?
        array_values($this->cache['Rfc']) :
        XPClass::forName('net.xp_framework.db.caffeine.Rfc')
          ->getMethod('getPeer')
          ->invoke(NULL)
          ->doSelect(new Criteria(
          array('rfc_id', $this->getRfc_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  net.xp_framework.db.caffeine.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= ($this->cached['Person']) ?
        array_values($this->cache['Person']) :
        XPClass::forName('net.xp_framework.db.caffeine.Person')
          ->getMethod('getPeer')
          ->invoke(NULL)
          ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>