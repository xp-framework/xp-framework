<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent a user
   *
   * @purpose  Base class
   */
  class User extends Object {
    var
      $person   = NULL,
      $language = '';

    /**
     * Set Person
     *
     * @access  public
     * @param   &org.apache.xml.workflow.AbstractPerson person
     */
    function setPerson(&$person) {
      $this->person= &$person;
    }

    /**
     * Get Person
     *
     * @access  public
     * @return  &org.apache.xml.workflow.AbstractPerson
     */
    function &getPerson() {
      return $this->person;
    }
    
    /**
     * Returns whether a user is logged on, which is when the
     * member variable "person" is not NULL.
     *
     * @access  public
     * @return  bool
     */
    function isLoggedOn() {
      return (NULL === $this->person);
    }
  }
?>
