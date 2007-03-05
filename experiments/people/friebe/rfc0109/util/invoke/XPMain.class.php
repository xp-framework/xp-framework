<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents main scope
   *
   * @see      xp://util.invoke.InvocationContext
   * @purpose  Specialized XPClass
   */
  class XPMain extends XPClass {
    private
      $entrypoint= NULL;

    /**
     * Constructor
     *
     * @param   mixed ref either a class name or an object
     */
    public function __construct($ref) {
      parent::__construct(__CLASS__);
      $this->entrypoint= newinstance('lang.reflect.Routine', array($this, 'main'), '{
        public function invoke($obj, $args= array()) {
          throw new IllegalStateException("Main scope entry point cannot be invoked");
        }
      }');
    }

    /**
     * Retrieves the main scope's name ([main])
     * 
     * @return  string
     */
    public function getName() {
      return '<main>';
    }
  
    /**
     * Throws an IllegalStateException - Main scope cannot be instantiated
     *
     * @param   mixed* args
     * @return  lang.Object 
     */
    public function newInstance() {
      throw new IllegalStateException('Main scope cannot be instantiated');
    }

    /**
     * Gets class methods for main scope - always an empty array
     *
     * @return  lang.reflect.Method[]
     */
    public function getMethods() {
      return array();
    }

    /**
     * Gets a Routine object representing this class' main method.
     *
     * @param   string name
     * @return  lang.Routine
     * @see     xp://lang.reflect.Routine
     */
    public function getMethod($name) {
      return $this->entrypoint;
    }

    /**
     * Checks whether this class has a method named "$method" or not.
     *
     * @param   string method the method's name
     * @return  bool TRUE if method exists
     */
    public function hasMethod($method) {
      return TRUE;
    }

    /**
     * Represents main scope entry point
     *
     */
    public function main() { }
  }
?>
