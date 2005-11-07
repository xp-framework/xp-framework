<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Routine');

  /**
   * Represents a class method
   *
   * @see      xp://lang.XPClass
   * @see      xp://lang.reflect.Routine
   * @purpose  Reflection
   */
  class Method extends Routine {
    
    /**
     * Invokes the underlying method represented by this Method object, 
     * on the specified object with the specified parameters.
     *
     * Example:
     * <code>
     *   $class= &XPClass::forName('lang.Object');
     *   $method= &$class->getMethod('toString');
     *
     *   var_dump($method->invoke(new Object()));
     * </code>
     *
     * Example (passing arguments)
     * <code>
     *   $class= &XPClass::forName('text.String');
     *   $method= &$class->getMethod('matches');
     *
     *   var_dump($method->invoke(new String('Hello'), array('/^H/')));
     * </code>
     *
     * Example (static invokation):
     * <code>
     *   $class= &XPClass::forName('util.log.Logger');
     *   $method= &$class->getMethod('getInstance');
     *
     *   var_dump($method->invoke($obj= NULL));
     * </code>
     *
     * @access  public
     * @param   &lang.Object obj
     * @param   mixed[] args default array()
     * @return  &mixed
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     */
    function &invoke(&$obj, $args= array()) {
      if (is_null($obj)) {
        return call_user_func_array(array($this->_ref, $this->name), $args);
      }
      
      if (!is(xp::nameOf($this->_ref), $obj)) {
        return throw(new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::nameOf($obj)
        )));
      }
      
      return call_user_func_array(array(&$obj, $this->name), $args);
    }
  }
?>
