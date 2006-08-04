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
  class ExceptionReference extends XPException {
    public 
      $classname= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string classname
     */
    public function __construct($classname) {
      $this->classname= $classname;
    }

    /**
     * Get Classname
     *
     * @access  public
     * @return  string
     */
    public function getClassname() {
      return $this->classname;
    }
  }
?>
