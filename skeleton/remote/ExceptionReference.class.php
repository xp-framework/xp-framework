<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.ChainedException');

  /**
   * Holds a reference to an exception
   *
   * @see      xp://remote.Serializer
   * @purpose  Exception reference
   */
  class ExceptionReference extends ChainedException {
    var 
      $referencedClassname= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string classname
     */
    function __construct($classname) {
      parent::__construct('(null)', $cause= NULL);
      $this->referencedClassname= $classname;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @access  public
     * @return  string
     */
    function compoundMessage() {
      return sprintf(
        'Exception %s<%s> (%s)',
        $this->getClassName(),
        $this->referencedClassname,
        $this->message
      );
    }
  }
?>
