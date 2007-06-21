<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 10625 2007-06-15 15:04:07Z friebe $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table ntexture, database Ruben_Test_PS
   * (Auto-generated on Wed, 20 Jun 2007 08:56:00 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ntexture extends DataSet {
    public
      $texture_id         = 0,
      $name               = '',
      $color_id           = 0;
  
    protected
      $cache= array(
        'Color' => array(),
        'NmappointTexture' => array(),
      );

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
        $peer->setRelations(array(
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int texture_id
     * @return  de.schlund.db.rubentest.Ntexture entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTexture_id($texture_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('texture_id', $texture_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "color_for_texture"
     * 
     * @param   int color_id
     * @return  de.schlund.db.rubentest.Ntexture[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColor_id($color_id) {
      return self::getPeer()->doSelect(new Criteria(array('color_id', $color_id, EQUAL)));
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
      $r= ($this->cached['Color']) ?
        array_values($this->cache['Color']) :
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
      if ($this->cached['NmappointTexture']) return array_values($this->cache['NmappointTexture']);
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
      if ($this->cached['NmappointTexture']) return new HashmapIterator($this->cache['NmappointTexture']);
      return XPClass::forName('de.schlund.db.rubentest.Nmappoint')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }
  }
?>