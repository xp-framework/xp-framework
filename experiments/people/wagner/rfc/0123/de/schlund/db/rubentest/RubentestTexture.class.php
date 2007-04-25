<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table texture, database Ruben_Test_PS
   * (Auto-generated on Thu, 29 Mar 2007 10:47:53 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestTexture extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.texture');
        $peer->setConnection('localhost');
        $peer->setIdentity('texture_id');
        $peer->setPrimary(array('texture_id'));
        $peer->setTypes(array(
          'texture_id'          => array('%d', FieldType::INT, TRUE),
          'name'                => array('%s', FieldType::VARCHAR, TRUE),
          'color_id'            => array('%d', FieldType::INT, TRUE)
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
     * @param   int texture_id
     * @return  de.schlund.db.rubentest.RubentestTexture entitiy object
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
     * @return  de.schlund.db.rubentest.RubentestTexture[] entity objects
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
  }
?>