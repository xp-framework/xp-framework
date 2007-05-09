<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table ncolor, database Ruben_Test_PS
   * (Auto-generated on Wed, 09 May 2007 14:59:38 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ncolor extends DataSet {
    public
      $color_id           = 0,
      $name               = '',
      $colortype_id       = 0;
  
    private
      $_cached=   array(),
  
      $cacheColortype= array(),
      $cacheNtextureColor= array();
  
    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.ncolor');
        $peer->setConnection('localhost');
        $peer->setIdentity('color_id');
        $peer->setPrimary(array('color_id'));
        $peer->setTypes(array(
          'color_id'            => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'colortype_id'        => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setConstraints(array(
          'Colortype' => array(
            'classname' => 'de.schlund.db.rubentest.Ncolortype',
            'key'       => array(
              'colortype_id' => 'colortype_id',
            ),
          ),
          'NtextureColor' => array(
            'classname' => 'de.schlund.db.rubentest.Ntexture',
            'key'       => array(
              'color_id' => 'color_id',
            ),
          ),
        ));
      }
    }  

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }
    public function _cacheGetColortype($key) { return $this->cacheColortype[$key]; }
    public function _cacheHasColortype($key) { return isset($this->cacheColortype[$key]); }
    public function _cacheAddColortype($key, $obj) { $this->cacheColortype[$key]= $obj; }
    public function _cacheGetNtextureColor($key) { return $this->cacheNtextureColor[$key]; }
    public function _cacheHasNtextureColor($key) { return isset($this->cacheNtextureColor[$key]); }
    public function _cacheAddNtextureColor($key, $obj) { $this->cacheNtextureColor[$key]= $obj; }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
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
     * @param   int color_id
     * @return  de.schlund.db.rubentest.Ncolor entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColor_id($color_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('color_id', $color_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

    /**
     * Gets an instance of this object by index "colortype_for_color"
     * 
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.Ncolor[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype_id($colortype_id) {
      return self::getPeer()->doSelect(new Criteria(array('colortype_id', $colortype_id, EQUAL)));    }

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
     * Retrieves colortype_id
     *
     * @return  int
     */
    public function getColortype_id() {
      return $this->colortype_id;
    }
      
    /**
     * Sets colortype_id
     *
     * @param   int colortype_id
     * @return  int the previous value
     */
    public function setColortype_id($colortype_id) {
      return $this->_change('colortype_id', $colortype_id);
    }

    /**
     * Retrieves the Ncolortype entity
     * referenced by colortype_id=>colortype_id
     *
     * @return  de.schlund.db.rubentest.Ncolortype entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColortype() {
      $r= ($this->_cached['Colortype']) ?
        array_values($this->cacheColortype) :
        XPClass::forName('de.schlund.db.rubentest.Ncolortype')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('colortype_id', $this->getColortype_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Ntexture entities referencing
     * this entity by color_id=>color_id
     *
     * @return  de.schlund.db.rubentest.Ntexture[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNtextureColorList() {
      if ($this->_cached['NtextureColor']) return array_values($this->cacheNtextureColor);
      return XPClass::forName('de.schlund.db.rubentest.Ntexture')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ntexture entities referencing
     * this entity by color_id=>color_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Ntexture>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNtextureColorIterator() {
      if ($this->_cached['NtextureColor']) return new HashmapIterator($this->cacheNtextureColor);
      return XPClass::forName('de.schlund.db.rubentest.Ntexture')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
    }
  }
?>