<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract Person.
   *
   * To be used in the basic user class
   *
   * @purpose  Represent person
   */
  class AbstractPerson extends Object {
    public 
      $name      = '',
      $firstname = '',
      $email     = '',
      $rights    = array();
            
    /**
     * Returns true if the person has the given right
     *
     * @access  public
     * @params  string rightname
     */
    public function hasRight($rightname) {
      return array_search($rightname, $this->rights, TRUE);
    }

  }
?>
