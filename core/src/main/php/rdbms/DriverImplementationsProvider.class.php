<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Driver implementations provider for driver manager
   *
   * @see   xp://rdbms.DefaultDrivers
   */
  abstract class DriverImplementationsProvider extends Object {
    protected $parent= NULL;

    /**
     * Constructor
     *
     * @param   rdbms.DriverImplementationsProvider parent
     */
    public function __construct(self $parent= NULL) {
      $this->parent= $parent;
    }
    
    /**
     * Returns an array of class names implementing a given driver
     *
     * @param   string driver
     * @return  string[] implementations
     */
    public function implementationsFor($driver) {
      return NULL === $this->parent ? array() : $this->parent->implementationsFor($driver);
    }
    
    /**
     * Creates a string representation of this implementation provider
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().(NULL === $this->parent ? '' : ', '.$this->parent->toString());
    }
  }
?>
