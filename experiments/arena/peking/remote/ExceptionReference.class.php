<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Holds a reference to an exception
   *
   * @see      xp://remote.Serializer
   * @purpose  Exception reference
   */
  class ExceptionReference extends Exception {
    var 
      $classname= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string classname
     */
    function __construct($classname) {
      $this->classname= $classname;
    }

    /**
     * Get Classname
     *
     * @access  public
     * @return  string
     */
    function getClassname() {
      return $this->classname;
    }
  }
?>
