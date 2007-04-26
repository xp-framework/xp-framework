<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table category_type_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Category_type_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..category_type_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('category_id'));
        $peer->setTypes(array(
          'category_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'category_type_id'    => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_CATEGORY_TYPE_MATRIX"
     * 
     * @param   int category_id
     * @return  de.schlund.db.methadon.Category_type_matrix entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCategory_id($category_id) {
      return new self(array(
        'category_id'  => $category_id,
        '_loadCrit' => new Criteria(array('category_id', $category_id, EQUAL))
      ));
    }

    /**
     * Retrieves category_id
     *
     * @return  int
     */
    public function getCategory_id() {
      return $this->category_id;
    }
      
    /**
     * Sets category_id
     *
     * @param   int category_id
     * @return  int the previous value
     */
    public function setCategory_id($category_id) {
      return $this->_change('category_id', $category_id);
    }

    /**
     * Retrieves category_type_id
     *
     * @return  int
     */
    public function getCategory_type_id() {
      return $this->category_type_id;
    }
      
    /**
     * Sets category_type_id
     *
     * @param   int category_type_id
     * @return  int the previous value
     */
    public function setCategory_type_id($category_type_id) {
      return $this->_change('category_type_id', $category_type_id);
    }

    /**
     * Retrieves the Category entity
     * referenced by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Category entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory() {
      $r= XPClass::forName('de.schlund.db.methadon.Category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Category_type entity
     * referenced by category_type_id=>category_type_id
     *
     * @return  de.schlund.db.methadon.Category_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Category_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_type_id', $this->getCategory_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>