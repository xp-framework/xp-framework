<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table components, database bugs
   * (Auto-generated on Tue, 25 Jan 2005 12:15:23 +0100 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaComponents extends DataSet {
    var
      $value              = NULL,
      $program            = NULL,
      $initialowner       = 0,
      $initialqacontact   = 0,
      $description        = '';

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &BugzillaComponents::getPeer()); {
        $peer->setTable('components');
        $peer->setConnection('bugzilla');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'value'               => '%s',
          'program'             => '%s',
          'initialowner'        => '%d',
          'initialqacontact'    => '%d',
          'description'         => '%s'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    function &getPeer() {
      return Peer::forName(__CLASS__);
    }
    
    /**
     * Gets an instance of this object by "value"
     *
     * @access  static
     * @param   string value
     * @return  &BugzillaComponents object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByValue($value) {
      $peer= &BugzillaComponents::getPeer();
      return $peer->doSelect(new Criteria(array('value', $value, EQUAL)));
    }    
  
    /**
     * Retrieves value
     *
     * @access  public
     * @return  string
     */
    function getValue() {
      return $this->value;
    }
      
    /**
     * Sets value
     *
     * @access  public
     * @param   string value
     * @return  string the previous value
     */
    function setValue($value) {
      return $this->_change('value', $value);
    }

    /**
     * Retrieves program
     *
     * @access  public
     * @return  string
     */
    function getProgram() {
      return $this->program;
    }
      
    /**
     * Sets program
     *
     * @access  public
     * @param   string program
     * @return  string the previous value
     */
    function setProgram($program) {
      return $this->_change('program', $program);
    }

    /**
     * Retrieves initialowner
     *
     * @access  public
     * @return  int
     */
    function getInitialowner() {
      return $this->initialowner;
    }
      
    /**
     * Sets initialowner
     *
     * @access  public
     * @param   int initialowner
     * @return  int the previous value
     */
    function setInitialowner($initialowner) {
      return $this->_change('initialowner', $initialowner);
    }

    /**
     * Retrieves initialqacontact
     *
     * @access  public
     * @return  int
     */
    function getInitialqacontact() {
      return $this->initialqacontact;
    }
      
    /**
     * Sets initialqacontact
     *
     * @access  public
     * @param   int initialqacontact
     * @return  int the previous value
     */
    function setInitialqacontact($initialqacontact) {
      return $this->_change('initialqacontact', $initialqacontact);
    }

    /**
     * Retrieves description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @access  public
     * @param   string description
     * @return  string the previous value
     */
    function setDescription($description) {
      return $this->_change('description', $description);
    }
  }
?>
