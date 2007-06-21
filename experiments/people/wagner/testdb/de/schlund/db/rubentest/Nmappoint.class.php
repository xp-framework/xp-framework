<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 10625 2007-06-15 15:04:07Z friebe $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table nmappoint, database Ruben_Test_PS
   * (Auto-generated on Wed, 20 Jun 2007 08:56:00 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Nmappoint extends DataSet {
    public
      $coord_x            = 0,
      $coord_y            = 0,
      $texture_id         = 0;
  
    protected
      $cache= array(
        'Texture' => array(),
        'NmobileObjectCoord_xCoord_y' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.nmappoint');
        $peer->setConnection('localhost');
        $peer->setPrimary(array('coord_x', 'coord_y'));
        $peer->setTypes(array(
          'coord_x'             => array('%d', FieldType::INT, FALSE),
          'coord_y'             => array('%d', FieldType::INT, FALSE),
          'texture_id'          => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Texture' => array(
            'classname' => 'de.schlund.db.rubentest.Ntexture',
            'key'       => array(
              'texture_id' => 'texture_id',
            ),
          ),
          'NmobileObjectCoord_xCoord_y' => array(
            'classname' => 'de.schlund.db.rubentest.Nmobileobject',
            'key'       => array(
              'coord_x' => 'coord_x','coord_y' => 'coord_y',
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
     * @param   int coord_x
     * @param   int coord_y
     * @return  de.schlund.db.rubentest.Nmappoint entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCoord_xCoord_y($coord_x, $coord_y) {
      $r= self::getPeer()->doSelect(new Criteria(
        array('coord_x', $coord_x, EQUAL),
        array('coord_y', $coord_y, EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "texture_for_mappoint"
     * 
     * @param   int texture_id
     * @return  de.schlund.db.rubentest.Nmappoint[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTexture_id($texture_id) {
      return self::getPeer()->doSelect(new Criteria(array('texture_id', $texture_id, EQUAL)));
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
     * Retrieves the Ntexture entity
     * referenced by texture_id=>texture_id
     *
     * @return  de.schlund.db.rubentest.Ntexture entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTexture() {
      $r= ($this->cached['Texture']) ?
        array_values($this->cache['Texture']) :
        XPClass::forName('de.schlund.db.rubentest.Ntexture')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Nmobileobject entities referencing
     * this entity by coord_x=>coord_x, coord_y=>coord_y
     *
     * @return  de.schlund.db.rubentest.Nmobileobject[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNmobileObjectCoord_xCoord_yList() {
      if ($this->cached['NmobileObjectCoord_xCoord_y']) return array_values($this->cache['NmobileObjectCoord_xCoord_y']);
      return XPClass::forName('de.schlund.db.rubentest.Nmobileobject')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('coord_x', $this->getCoord_x(), EQUAL),
          array('coord_y', $this->getCoord_y(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Nmobileobject entities referencing
     * this entity by coord_x=>coord_x, coord_y=>coord_y
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Nmobileobject>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNmobileObjectCoord_xCoord_yIterator() {
      if ($this->cached['NmobileObjectCoord_xCoord_y']) return new HashmapIterator($this->cache['NmobileObjectCoord_xCoord_y']);
      return XPClass::forName('de.schlund.db.rubentest.Nmobileobject')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('coord_x', $this->getCoord_x(), EQUAL),
          array('coord_y', $this->getCoord_y(), EQUAL)
      ));
    }
  }
?>