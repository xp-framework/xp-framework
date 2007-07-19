<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table ncolortype, database Ruben_Test_PS
   * (Auto-generated on Thu, 19 Jul 2007 12:49:17 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ncolortype extends DataSet {
    public
      $colortype_id       = 0,
      $name               = '';
  
    protected
      $cache= array(
        'NcolorColortype' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.ncolortype');
        $peer->setConnection('localhost');
        $peer->setIdentity('colortype_id');
        $peer->setPrimary(array('colortype_id'));
        $peer->setTypes(array(
          'colortype_id'        => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE)
        ));
        $peer->setRelations(array(
          'NcolorColortype' => array(
            'classname' => 'de.schlund.db.rubentest.Ncolor',
            'key'       => array(
              'colortype_id' => 'colortype_id',
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
     * @throws  lang.IllegalArgumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.Ncolortype entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype_id($colortype_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('colortype_id', $colortype_id, EQUAL)));
      return $r ? $r[0] : NULL;
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
     * Retrieves an array of all Ncolor entities referencing
     * this entity by colortype_id=>colortype_id
     *
     * @return  de.schlund.db.rubentest.Ncolor[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNcolorColortypeList() {
      if ($this->cached['NcolorColortype']) return array_values($this->cache['NcolorColortype']);
      return XPClass::forName('de.schlund.db.rubentest.Ncolor')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('colortype_id', $this->getColortype_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ncolor entities referencing
     * this entity by colortype_id=>colortype_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Ncolor
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNcolorColortypeIterator() {
      if ($this->cached['NcolorColortype']) return new HashmapIterator($this->cache['NcolorColortype']);
      return XPClass::forName('de.schlund.db.rubentest.Ncolor')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('colortype_id', $this->getColortype_id(), EQUAL)
      ));
    }
  }
?>