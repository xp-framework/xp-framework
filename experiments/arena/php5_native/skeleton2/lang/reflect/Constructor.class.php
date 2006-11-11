<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Routine');

  /**
   * Represents a class' constructor
   *
   * @see      xp://lang.XPClass
   * @see      xp://lang.reflect.Routine
   * @purpose  Reflection
   */
  class Constructor extends Routine {

    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed ref
     */    
    public function __construct(&$ref) {
      parent::__construct($ref, '__construct');
    }
    
    /**
     * Uses the constructor represented by this Constructor object to create 
     * and initialize a new instance of the constructor's declaring class, 
     * with the specified initialization parameters.
     *
     * Example:
     * <code>
     *   $class= &XPClass::forName('lang.Object');
     *   $constructor= &$class->getConstructor();
     *
     *   var_dump($constructor->newInstance());
     * </code>
     *
     * @access  public
     * @param   mixed* args
     * @return  &lang.Object
     */
    public function &newInstance() {
      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      return eval('return new '.$this->_ref.'('.substr($paramstr, 2).');');
    }

    /**
     * Retrieve return type
     *
     * @access  public
     * @return  string
     */
    public function getReturnType() {
      return xp::nameOf($this->_ref);
    }
  }
?>
