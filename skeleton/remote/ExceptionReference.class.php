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
      $referencedClassname= '';

    /**
     * Constructor
     *
     * @param   string classname
     */
    public function __construct($classname) {
      parent::__construct('(null)', $cause= NULL);
      $this->referencedClassname= $classname;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        'Exception %s<%s> (%s)',
        $this->getClassName(),
        $this->referencedClassname,
        $this->message
      );
    }
  }
?>
