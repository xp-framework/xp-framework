<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table category, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Category extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..category');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('category_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'category_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'category_features'   => array('%d', FieldType::INTN, TRUE)
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
     * Gets an instance of this object by index "PK_category"
     * 
     * @param   int category_id
     * @return  de.schlund.db.methadon.Category entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCategory_id($category_id) {
      return new self(array(
        'category_id'  => $category_id,
        '_loadCrit' => new Criteria(array('category_id', $category_id, EQUAL))
      ));
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
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string the previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
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
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }

    /**
     * Retrieves category_features
     *
     * @return  int
     */
    public function getCategory_features() {
      return $this->category_features;
    }
      
    /**
     * Sets category_features
     *
     * @param   int category_features
     * @return  int the previous value
     */
    public function setCategory_features($category_features) {
      return $this->_change('category_features', $category_features);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>category_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getCategory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Event entities referencing
     * this entity by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Event[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event entities referencing
     * this entity by category_id=>category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_category_todo entities referencing
     * this entity by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Person_category_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_todoCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_category_todo entities referencing
     * this entity by category_id=>category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_category_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_todoCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves the Category_type_matrix entity referencing
     * this entity by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Category_type_matrix entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory_type_matrixCategory() {
      $r= XPClass::forName('de.schlund.db.methadon.Category_type_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Person_category_matrix entities referencing
     * this entity by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Person_category_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_matrixCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_category_matrix entities referencing
     * this entity by category_id=>category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_category_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_matrixCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_template entities referencing
     * this entity by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Event_template[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_template entities referencing
     * this entity by category_id=>category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_template>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
    }
  }
?>