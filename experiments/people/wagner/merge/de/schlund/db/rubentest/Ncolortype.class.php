<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table ncolortype, database Ruben_Test_PS
   * (Auto-generated on Mon, 23 Apr 2007 18:37:45 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ncolortype extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL,
      $_cached=   array();

    private
      $cacheNcolorColortype= array();
  
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

    public function _cacheMark($role) { $this->_cached[$role]= TRUE; }
    public function _cacheGetNcolorColortype($key) { return $this->cacheNcolorColortype[$key]; }
    public function _cacheHasNcolorColortype($key) { return isset($this->cacheNcolorColortype[$key]); }
    public function _cacheAddNcolorColortype($key, $obj) { $this->cacheNcolorColortype[$key]= $obj; }

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
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.Ncolortype entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByColortype_id($colortype_id) {
      return new self(array(
        'colortype_id'  => $colortype_id,
        '_loadCrit' => new Criteria(array('colortype_id', $colortype_id, EQUAL))
      ));
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
      if ($this->_cached['NcolorColortype']) return array_values($this->cacheNcolorColortype);
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
      if ($this->_cached['NcolorColortype']) return new HashmapIterator($this->cacheNcolorColortype);
      return XPClass::forName('de.schlund.db.rubentest.Ncolor')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('colortype_id', $this->getColortype_id(), EQUAL)
      ));
    }
  }
?>