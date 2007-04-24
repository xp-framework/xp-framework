<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table nmobileObject, database Ruben_Test_PS
   * (Auto-generated on Mon, 23 Apr 2007 18:37:45 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Nmobileobject extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL,
      $_cached=   array();

    private
      $cacheCoord_xCoord_y= array();
  
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.nmobileObject');
        $peer->setConnection('localhost');
        $peer->setIdentity('object_id');
        $peer->setPrimary(array('object_id'));
        $peer->setTypes(array(
          'object_id'           => array('%d', FieldType::INT, FALSE),
          'coord_x'             => array('%d', FieldType::INT, FALSE),
          'coord_y'             => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE)
        ));
        $peer->setConstraints(array(
          'Coord_xCoord_y' => array(
            'classname' => 'de.schlund.db.rubentest.Nmappoint',
            'key'       => array(
              'coord_x' => 'coord_x','coord_y' => 'coord_y',
            ),
          ),
        ));
      }
    }  

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }
    public function _cacheGetCoord_xCoord_y($key) { return $this->cacheCoord_xCoord_y[$key]; }
    public function _cacheHasCoord_xCoord_y($key) { return isset($this->cacheCoord_xCoord_y[$key]); }
    public function _cacheAddCoord_xCoord_y($key, $obj) { $this->cacheCoord_xCoord_y[$key]= $obj; }

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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int object_id
     * @return  de.schlund.db.rubentest.Nmobileobject entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByObject_id($object_id) {
      return new self(array(
        'object_id'  => $object_id,
        '_loadCrit' => new Criteria(array('object_id', $object_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "coords"
     * 
     * @param   int coord_x
     * @param   int coord_y
     * @return  de.schlund.db.rubentest.Nmobileobject[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCoord_xCoord_y($coord_x, $coord_y) {
      $r= self::getPeer()->doSelect(new Criteria(
        array('coord_x', $coord_x, EQUAL),
        array('coord_y', $coord_y, EQUAL)
      ));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves object_id
     *
     * @return  int
     */
    public function getObject_id() {
      return $this->object_id;
    }
      
    /**
     * Sets object_id
     *
     * @param   int object_id
     * @return  int the previous value
     */
    public function setObject_id($object_id) {
      return $this->_change('object_id', $object_id);
    }

    /**
     * Retrieves coord_x
     *
     * @return  int
     */
    public function getCoord_x() {
      return $this->coord_x;
    }
      
    /**
     * Sets coord_x
     *
     * @param   int coord_x
     * @return  int the previous value
     */
    public function setCoord_x($coord_x) {
      return $this->_change('coord_x', $coord_x);
    }

    /**
     * Retrieves coord_y
     *
     * @return  int
     */
    public function getCoord_y() {
      return $this->coord_y;
    }
      
    /**
     * Sets coord_y
     *
     * @param   int coord_y
     * @return  int the previous value
     */
    public function setCoord_y($coord_y) {
      return $this->_change('coord_y', $coord_y);
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves the Nmappoint entity
     * referenced by coord_x=>coord_x, coord_y=>coord_y
     *
     * @return  de.schlund.db.rubentest.Nmappoint entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCoord_xCoord_y() {
      $r= ($this->_cached['Coord_xCoord_y']) ?
        array_values($this->cacheCoord_xCoord_y) :
        XPClass::forName('de.schlund.db.rubentest.Nmappoint')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('coord_x', $this->getCoord_x(), EQUAL),
          array('coord_y', $this->getCoord_y(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>