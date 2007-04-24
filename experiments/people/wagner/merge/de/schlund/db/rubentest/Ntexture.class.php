<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table ntexture, database Ruben_Test_PS
   * (Auto-generated on Tue, 24 Apr 2007 16:43:57 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ntexture extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL,
      $_cached=   array();

    private
      $cacheColor= array(),
      $cacheNmappointTexture= array();
  
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.ntexture');
        $peer->setConnection('localhost');
        $peer->setIdentity('texture_id');
        $peer->setPrimary(array('texture_id'));
        $peer->setTypes(array(
          'texture_id'          => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'color_id'            => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setConstraints(array(
          'Color' => array(
            'classname' => 'de.schlund.db.rubentest.Ncolor',
            'key'       => array(
              'color_id' => 'color_id',
            ),
          ),
          'NmappointTexture' => array(
            'classname' => 'de.schlund.db.rubentest.Nmappoint',
            'key'       => array(
              'texture_id' => 'texture_id',
            ),
          ),
        ));
      }
    }  

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }
    public function _cacheGetColor($key) { return $this->cacheColor[$key]; }
    public function _cacheHasColor($key) { return isset($this->cacheColor[$key]); }
    public function _cacheAddColor($key, $obj) { $this->cacheColor[$key]= $obj; }
    public function _cacheGetNmappointTexture($key) { return $this->cacheNmappointTexture[$key]; }
    public function _cacheHasNmappointTexture($key) { return isset($this->cacheNmappointTexture[$key]); }
    public function _cacheAddNmappointTexture($key, $obj) { $this->cacheNmappointTexture[$key]= $obj; }

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
     * @param   int texture_id
     * @return  de.schlund.db.rubentest.Ntexture entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTexture_id($texture_id) {
      return new self(array(
        'texture_id'  => $texture_id,
        '_loadCrit' => new Criteria(array('texture_id', $texture_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "color_for_texture"
     * 
     * @param   int color_id
     * @return  de.schlund.db.rubentest.Ntexture[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColor_id($color_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('color_id', $color_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves texture_id
     *
     * @return  int
     */
    public function getTexture_id() {
      return $this->texture_id;
    }
      
    /**
     * Sets texture_id
     *
     * @param   int texture_id
     * @return  int the previous value
     */
    public function setTexture_id($texture_id) {
      return $this->_change('texture_id', $texture_id);
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
     * Retrieves color_id
     *
     * @return  int
     */
    public function getColor_id() {
      return $this->color_id;
    }
      
    /**
     * Sets color_id
     *
     * @param   int color_id
     * @return  int the previous value
     */
    public function setColor_id($color_id) {
      return $this->_change('color_id', $color_id);
    }

    /**
     * Retrieves the Ncolor entity
     * referenced by color_id=>color_id
     *
     * @return  de.schlund.db.rubentest.Ncolor entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColor() {
      $r= ($this->_cached['Color']) ?
        array_values($this->cacheColor) :
        XPClass::forName('de.schlund.db.rubentest.Ncolor')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Nmappoint entities referencing
     * this entity by texture_id=>texture_id
     *
     * @return  de.schlund.db.rubentest.Nmappoint[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNmappointTextureList() {
      if ($this->_cached['NmappointTexture']) return array_values($this->cacheNmappointTexture);
      return XPClass::forName('de.schlund.db.rubentest.Nmappoint')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Nmappoint entities referencing
     * this entity by texture_id=>texture_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Nmappoint>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNmappointTextureIterator() {
      if ($this->_cached['NmappointTexture']) return new HashmapIterator($this->cacheNmappointTexture);
      return XPClass::forName('de.schlund.db.rubentest.Nmappoint')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }
  }
?>