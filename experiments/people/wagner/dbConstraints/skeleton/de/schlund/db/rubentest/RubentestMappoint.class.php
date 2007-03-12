<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses(
    'rdbms.DataSet',
    'de.schlund.db.rubentest.RubentestTexture'
  );

  /**
   * Class wrapper for table mappoint, database Ruben_Test_PS
   * (Auto-generated on Mon, 12 Mar 2007 16:54:40 +0100 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestMappoint extends DataSet {
    public
      $coord_x            = 0,
      $coord_y            = 0,
      $texture_id         = 0;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.mappoint');
        $peer->setConnection('localhost');
        $peer->setPrimary(array('coord_x', 'coord_y'));
        $peer->setTypes(array(
          'coord_x'             => array('%d', FieldType::INT, FALSE),
          'coord_y'             => array('%d', FieldType::INT, FALSE),
          'texture_id'          => array('%d', FieldType::INT, FALSE)
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
     * @param   int coord_x
     * @param   int coord_y
     * @return  de.schlund.db.rubentest.RubentestMappoint entitiy object
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
     * @return  de.schlund.db.rubentest.RubentestMappoint[] entities object
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
     * Retrieves the referenced Texture
     *
     * @return  de.schlund.db.rubentest.RubentestTexture entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTexture() {
      $r= RubentestTexture::getPeer()->doSelect(new Criteria(
        array('texture_id', $this->getTexture_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of the referencing Mobileobject
     *
     * @return  de.schlund.db.rubentest.RubentestMobileobject[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMobileobjectList() {
      return RubentestMobileobject::getPeer()->doSelect(new Criteria(
        array('coord_x', $this->getCoord_x(), EQUAL),
        array('coord_y', $this->getCoord_y(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for the referencing Mobileobject
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestMobileobject>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMobileobjectIterator() {
      return RubentestMobileobject::getPeer()->iteratorFor(new Criteria(
        array('coord_x', $this->getCoord_x(), EQUAL),
        array('coord_y', $this->getCoord_y(), EQUAL)
      ));
    }
  }
?>