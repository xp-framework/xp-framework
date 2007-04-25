<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table texture, database Ruben_Test_PS
   * (Auto-generated on Tue, 27 Mar 2007 18:08:00 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestTexture extends DataSet {
    public
      $texture_id         = 0,
      $name               = '',
      $color_id           = 0;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.texture');
        $peer->setConnection('localhost');
        $peer->setIdentity('texture_id');
        $peer->setPrimary(array('texture_id'));
        $peer->setTypes(array(
          'texture_id'          => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'color_id'            => array('%d', FieldType::INT, FALSE)
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int texture_id
     * @return  de.schlund.db.rubentest.RubentestTexture entitiy object
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
     * @return  de.schlund.db.rubentest.RubentestTexture[] entities object
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
     * Retrieves the Color entity
     * referenced by color_id=>color_id
     *
     * @return  de.schlund.db.rubentest.RubentestColor entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColor() {
      $r= XPClass::forName('de.schlund.db.rubentest.RubentestColor')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('color_id', $this->getColor_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Mappoint entities referencing
     * this entity by texture_id=>texture_id
     *
     * @return  de.schlund.db.rubentest.RubentestMappoint[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMappointTextureList() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestMappoint')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Mappoint entities referencing
     * this entity by texture_id=>texture_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestMappoint>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMappointTextureIterator() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestMappoint')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }
  }
?>