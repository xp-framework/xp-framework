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
      * Retrieve return type
      *
      * @access  public
      * @return  string
      */
     public function getReturnType() {
       return xp::nameOf($this->_reflection->getDeclaringClass()->getName());
     }
    
    /**
     * Uses the constructor represented by this Constructor object to create 
     * and initialize a new instance of the constructor's declaring class, 
     * with the specified initialization parameters.
     *
     * Example:
     * <code>
     *   $constructor= XPClass::forName('lang.Object')->getConstructor();
     *
     *   var_dump($constructor->newInstance());
     * </code>
     *
     * @access  public
     * @param   mixed* args
     * @return  &lang.Object
     */
    public function newInstance() {
      with ($c= $this->_reflection->getDeclaringClass()); {
        if (!$c->isInstantiable()) {
          throw (new InstantiationException($c->getName().' is not instantiable'));
        }
        $args= func_get_args();
        return call_user_func_array(array($c, 'newInstance'), $args);
      }
    }
  }
?>
