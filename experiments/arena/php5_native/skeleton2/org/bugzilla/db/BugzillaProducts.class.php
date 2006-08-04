<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table products, database bugs
   * (Auto-generated on Tue,  7 Jun 2005 13:16:29 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaProducts extends DataSet {
    public
      $name               = '',
      $description        = NULL,
      $milestoneurl       = '',
      $disallownew        = 0,
      $votesperuser       = '',
      $maxvotesperbug     = '',
      $votestoconfirm     = '',
      $defaultmilestone   = '',
      $id                 = '';

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() { 
      with ($peer= &BugzillaProducts::getPeer()); {
        $peer->setTable('products');
        $peer->setConnection('bugzilla');
        $peer->setIdentity('id');
        $peer->setPrimary(array('id'));
        $peer->setTypes(array(
          'name'                => '%s',
          'description'         => '%s',
          'milestoneurl'        => '%s',
          'disallownew'         => '%d',
          'votesperuser'        => '%s',
          'maxvotesperbug'      => '%s',
          'votestoconfirm'      => '%s',
          'defaultmilestone'    => '%s',
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
     * @return  &org.bugzilla.db.BugzillaProducts object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getById($id) {
      $peer= &BugzillaProducts::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('id', $id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "name"
     *
     * @access  static
     * @param   string name
     * @return  &org.bugzilla.db.BugzillaProducts object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByName($name) {
      $peer= &BugzillaProducts::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('name', $name, EQUAL))));
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
     * Retrieves milestoneurl
     *
     * @access  public
     * @return  string
     */
    public function getMilestoneurl() {
      return $this->milestoneurl;
    }
      
    /**
     * Sets milestoneurl
     *
     * @access  public
     * @param   string milestoneurl
     * @return  string the previous value
     */
    public function setMilestoneurl($milestoneurl) {
      return $this->_change('milestoneurl', $milestoneurl);
    }

    /**
     * Retrieves disallownew
     *
     * @access  public
     * @return  int
     */
    public function getDisallownew() {
      return $this->disallownew;
    }
      
    /**
     * Sets disallownew
     *
     * @access  public
     * @param   int disallownew
     * @return  int the previous value
     */
    public function setDisallownew($disallownew) {
      return $this->_change('disallownew', $disallownew);
    }

    /**
     * Retrieves votesperuser
     *
     * @access  public
     * @return  string
     */
    public function getVotesperuser() {
      return $this->votesperuser;
    }
      
    /**
     * Sets votesperuser
     *
     * @access  public
     * @param   string votesperuser
     * @return  string the previous value
     */
    public function setVotesperuser($votesperuser) {
      return $this->_change('votesperuser', $votesperuser);
    }

    /**
     * Retrieves maxvotesperbug
     *
     * @access  public
     * @return  string
     */
    public function getMaxvotesperbug() {
      return $this->maxvotesperbug;
    }
      
    /**
     * Sets maxvotesperbug
     *
     * @access  public
     * @param   string maxvotesperbug
     * @return  string the previous value
     */
    public function setMaxvotesperbug($maxvotesperbug) {
      return $this->_change('maxvotesperbug', $maxvotesperbug);
    }

    /**
     * Retrieves votestoconfirm
     *
     * @access  public
     * @return  string
     */
    public function getVotestoconfirm() {
      return $this->votestoconfirm;
    }
      
    /**
     * Sets votestoconfirm
     *
     * @access  public
     * @param   string votestoconfirm
     * @return  string the previous value
     */
    public function setVotestoconfirm($votestoconfirm) {
      return $this->_change('votestoconfirm', $votestoconfirm);
    }

    /**
     * Retrieves defaultmilestone
     *
     * @access  public
     * @return  string
     */
    public function getDefaultmilestone() {
      return $this->defaultmilestone;
    }
      
    /**
     * Sets defaultmilestone
     *
     * @access  public
     * @param   string defaultmilestone
     * @return  string the previous value
     */
    public function setDefaultmilestone($defaultmilestone) {
      return $this->_change('defaultmilestone', $defaultmilestone);
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
