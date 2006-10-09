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
     * Return string representation of this exception
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s<%s> (%s)\n",
        $this->getClassName(),
        $this->referencedClassname,
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s.($this->cause
        ? 'Caused by '.$this->cause->toString() 
        : ''
      );;
    }
  }
?>
