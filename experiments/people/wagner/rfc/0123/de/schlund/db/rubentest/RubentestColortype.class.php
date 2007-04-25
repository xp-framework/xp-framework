<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table colortype, database Ruben_Test_PS
   * (Auto-generated on Thu, 29 Mar 2007 10:47:53 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestColortype extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.colortype');
        $peer->setConnection('localhost');
        $peer->setIdentity('colortype_id');
        $peer->setPrimary(array('colortype_id'));
        $peer->setTypes(array(
          'colortype_id'        => array('%d', FieldType::INT, TRUE),
          'name'                => array('%s', FieldType::VARCHAR, TRUE)
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
     * @param   int colortype_id
     * @return  de.schlund.db.rubentest.RubentestColortype entitiy object
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
  }
?>