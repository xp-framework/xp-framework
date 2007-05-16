<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'rdbms.join.JoinExtractable', 'util.HashmapIterator');

  /**
   * Class wrapper for table ncolortype, database Ruben_Test_PS
   * (Auto-generated on Wed, 16 May 2007 14:44:35 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ncolortype extends DataSet implements JoinExtractable {
    public
      $colortype_id       = 0,
      $name               = '';
  
    private
      $cache= array(
        'NcolorColortype' => array(),
      ),
      $cached= array();

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
        $peer->setConstraints(array(
          'NcolorColortype' => array(
            'classname' => 'de.schlund.db.rubentest.Ncolor',
            'key'       => array(
              'colortype_id' => 'colortype_id',
            ),
          ),
        ));
      }
    }  

    public function setCachedObj($role, $key, $obj) { $this->cache[$role][$key]= $obj; }
    public function getCachedObj($role, $key)       { return $this->cache[$role][$key]; }
    public function hasCachedObj($role, $key)       { return isset($this->cache[$role][$key]); }
    public function markAsCached($role)             { $this->cached[$role]= TRUE; }
    
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
    static public function column($name) {
      return self::getPeer()->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.Ncolortype entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype_id($colortype_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('colortype_id', $colortype_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

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
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.Ncolor>
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