<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table color, database Ruben_Test_PS
   * (Auto-generated on Wed, 04 Apr 2007 10:45:30 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestColor extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.color');
        $peer->setConnection('localhost');
        $peer->setIdentity('color_id');
        $peer->setPrimary(array('color_id'));
        $peer->setTypes(array(
          'color_id'            => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'colortype_id'        => array('%d', FieldType::INT, FALSE)
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int color_id
     * @return  de.schlund.db.rubentest.RubentestColor entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColor_id($color_id) {
      return new self(array(
        'color_id'  => $color_id,
        '_loadCrit' => new Criteria(array('color_id', $color_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "colortype_for_color"
     * 
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.RubentestColor[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype_id($colortype_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('colortype_id', $colortype_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves the Colortype entity
     * referenced by colortype_id=>colortype_id
     *
     * @return  de.schlund.db.rubentest.RubentestColortype entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColortype() {
      $r= XPClass::forName('de.schlund.db.rubentest.RubentestColortype')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('colortype_id', $this->getColortype_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Texture entities referencing
     * this entity by color_id=>color_id
     *
     * @return  de.schlund.db.rubentest.RubentestTexture[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextureColorList() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestTexture')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Texture entities referencing
     * this entity by color_id=>color_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestTexture>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextureColorIterator() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestTexture')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
    }
  }
?>