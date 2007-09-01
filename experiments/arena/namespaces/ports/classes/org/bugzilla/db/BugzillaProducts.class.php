<?php
/* This class is part of the XP framework
 *
 * $Id: xp.php.xsl 5143 2005-05-18 12:34:24Z kiesel $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table products, database bugs
   * (Auto-generated on Tue,  7 Jun 2005 13:16:29 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaProducts extends rdbms::DataSet {
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
     */
    public static function __static() { 
      with ($peer= ::getPeer()); {
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
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @param   string id
     * @return  &org.bugzilla.db.BugzillaProducts object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getById($id) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('id', $id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "name"
     *
     * @param   string name
     * @return  &org.bugzilla.db.BugzillaProducts object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByName($name) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('name', $name, EQUAL))));
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
     * Retrieves milestoneurl
     *
     * @return  string
     */
    public function getMilestoneurl() {
      return $this->milestoneurl;
    }
      
    /**
     * Sets milestoneurl
     *
     * @param   string milestoneurl
     * @return  string the previous value
     */
    public function setMilestoneurl($milestoneurl) {
      return $this->_change('milestoneurl', $milestoneurl);
    }

    /**
     * Retrieves disallownew
     *
     * @return  int
     */
    public function getDisallownew() {
      return $this->disallownew;
    }
      
    /**
     * Sets disallownew
     *
     * @param   int disallownew
     * @return  int the previous value
     */
    public function setDisallownew($disallownew) {
      return $this->_change('disallownew', $disallownew);
    }

    /**
     * Retrieves votesperuser
     *
     * @return  string
     */
    public function getVotesperuser() {
      return $this->votesperuser;
    }
      
    /**
     * Sets votesperuser
     *
     * @param   string votesperuser
     * @return  string the previous value
     */
    public function setVotesperuser($votesperuser) {
      return $this->_change('votesperuser', $votesperuser);
    }

    /**
     * Retrieves maxvotesperbug
     *
     * @return  string
     */
    public function getMaxvotesperbug() {
      return $this->maxvotesperbug;
    }
      
    /**
     * Sets maxvotesperbug
     *
     * @param   string maxvotesperbug
     * @return  string the previous value
     */
    public function setMaxvotesperbug($maxvotesperbug) {
      return $this->_change('maxvotesperbug', $maxvotesperbug);
    }

    /**
     * Retrieves votestoconfirm
     *
     * @return  string
     */
    public function getVotestoconfirm() {
      return $this->votestoconfirm;
    }
      
    /**
     * Sets votestoconfirm
     *
     * @param   string votestoconfirm
     * @return  string the previous value
     */
    public function setVotestoconfirm($votestoconfirm) {
      return $this->_change('votestoconfirm', $votestoconfirm);
    }

    /**
     * Retrieves defaultmilestone
     *
     * @return  string
     */
    public function getDefaultmilestone() {
      return $this->defaultmilestone;
    }
      
    /**
     * Sets defaultmilestone
     *
     * @param   string defaultmilestone
     * @return  string the previous value
     */
    public function setDefaultmilestone($defaultmilestone) {
      return $this->_change('defaultmilestone', $defaultmilestone);
    }

    /**
     * Retrieves id
     *
     * @return  string
     */
    public function getId() {
      return $this->id;
    }
      
    /**
     * Sets id
     *
     * @param   string id
     * @return  string the previous value
     */
    public function setId($id) {
      return $this->_change('id', $id);
    }
  }
?>
