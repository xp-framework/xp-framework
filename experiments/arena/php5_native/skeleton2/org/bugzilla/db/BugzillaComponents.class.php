<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table components, database bugs
   * (Auto-generated on Tue,  7 Jun 2005 12:10:40 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaComponents extends DataSet {
    public
      $name               = '',
      $initialowner       = 0,
      $initialqacontact   = 0,
      $description        = '',
      $product_id         = '',
      $id                 = '';

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public function __static() { 
      with ($peer= &BugzillaComponents::getPeer()); {
        $peer->setTable('components');
        $peer->setConnection('bugzilla');
        $peer->setIdentity('id');
        $peer->setPrimary(array('id'));
        $peer->setTypes(array(
          'name'                => '%s',
          'initialowner'        => '%d',
          'initialqacontact'    => '%d',
          'description'         => '%s',
          'product_id'          => '%s',
          'id'                  => '%s'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    public function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   string id
     * @return  &org.bugzilla.db.BugzillaComponents object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getById($id) {
      $peer= &BugzillaComponents::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('id', $id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "product_id"
     *
     * @access  static
     * @param   string product_id
     * @param   string name
     * @return  &org.bugzilla.db.BugzillaComponents object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByProduct_idName($product_id, $name) {
      $peer= &BugzillaComponents::getPeer();
      return array_shift($peer->doSelect(new Criteria(
        array('product_id', $product_id, EQUAL),
        array('name', $name, EQUAL)
      )));
    }

    /**
     * Gets an instance of this object by index "name"
     *
     * @access  static
     * @param   string name
     * @return  &org.bugzilla.db.BugzillaComponents[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByName($name) {
      $peer= &BugzillaComponents::getPeer();
      return $peer->doSelect(new Criteria(array('name', $name, EQUAL)));
    }

    /**
     * Retrieves name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @access  public
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves initialowner
     *
     * @access  public
     * @return  int
     */
    public function getInitialowner() {
      return $this->initialowner;
    }
      
    /**
     * Sets initialowner
     *
     * @access  public
     * @param   int initialowner
     * @return  int the previous value
     */
    public function setInitialowner($initialowner) {
      return $this->_change('initialowner', $initialowner);
    }

    /**
     * Retrieves initialqacontact
     *
     * @access  public
     * @return  int
     */
    public function getInitialqacontact() {
      return $this->initialqacontact;
    }
      
    /**
     * Sets initialqacontact
     *
     * @access  public
     * @param   int initialqacontact
     * @return  int the previous value
     */
    public function setInitialqacontact($initialqacontact) {
      return $this->_change('initialqacontact', $initialqacontact);
    }

    /**
     * Retrieves description
     *
     * @access  public
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @access  public
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves product_id
     *
     * @access  public
     * @return  string
     */
    public function getProduct_id() {
      return $this->product_id;
    }
      
    /**
     * Sets product_id
     *
     * @access  public
     * @param   string product_id
     * @return  string the previous value
     */
    public function setProduct_id($product_id) {
      return $this->_change('product_id', $product_id);
    }

    /**
     * Retrieves id
     *
     * @access  public
     * @return  string
     */
    public function getId() {
      return $this->id;
    }
      
    /**
     * Sets id
     *
     * @access  public
     * @param   string id
     * @return  string the previous value
     */
    public function setId($id) {
      return $this->_change('id', $id);
    }
  }
?>
