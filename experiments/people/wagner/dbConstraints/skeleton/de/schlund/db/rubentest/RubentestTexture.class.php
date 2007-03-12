<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses(
    'rdbms.DataSet',
    'de.schlund.db.rubentest.RubentestColor'
  );

  /**
   * Class wrapper for table texture, database Ruben_Test_PS
   * (Auto-generated on Mon, 12 Mar 2007 16:54:40 +0100 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestTexture extends DataSet {
    public
      $texture_id         = 0,
      $name               = '',
      $colortype          = '';

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.texture');
        $peer->setConnection('localhost');
        $peer->setIdentity('texture_id');
        $peer->setPrimary(array('texture_id'));
        $peer->setTypes(array(
          'texture_id'          => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'colortype'           => array('%s', FieldType::VARCHAR, FALSE)
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
     * Gets an instance of this object by index "color_of_texture"
     * 
     * @param   string colortype
     * @return  de.schlund.db.rubentest.RubentestTexture[] entities object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype($colortype) {
      return self::getPeer()->doSelect(new Criteria(array('colortype', $colortype, EQUAL)));
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
     * Retrieves colortype
     *
     * @return  string
     */
    public function getColortype() {
      return $this->colortype;
    }
      
    /**
     * Sets colortype
     *
     * @param   string colortype
     * @return  string the previous value
     */
    public function setColortype($colortype) {
      return $this->_change('colortype', $colortype);
    }

    /**
     * Retrieves an array of the referenced Color
     *
     * @return  de.schlund.db.rubentest.RubentestColor[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColorList() {
      return RubentestColor::getPeer()->doSelect(new Criteria(
        array('colortype', $this->getColortype(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for the referenced Color
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestColor
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getColorIterator() {
      return RubentestColor::getPeer()->iteratorFor(new Criteria(
        array('colortype', $this->getColortype(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of the referencing Mappoint
     *
     * @return  de.schlund.db.rubentest.RubentestMappoint[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMappointList() {
      return RubentestMappoint::getPeer()->doSelect(new Criteria(
        array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for the referencing Mappoint
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestMappoint>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMappointIterator() {
      return RubentestMappoint::getPeer()->iteratorFor(new Criteria(
        array('texture_id', $this->getTexture_id(), EQUAL)
      ));
    }
  }
?>