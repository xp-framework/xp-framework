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
     *   $method= XPClass::forName('lang.Object')->getMethod('toString');
     *
     *   var_dump($method->invoke(new Object()));
     * </code>
     *
     * Example (passing arguments)
     * <code>
     *   $method= XPClass::forName('text.String')->getMethod('matches');
     *
     *   var_dump($method->invoke(new String('Hello'), array('/^H/')));
     * </code>
     *
     * Example (static invokation):
     * <code>
     *   $method= XPClass::forName'util.log.Logger')->getMethod('getInstance');
     *
     *   var_dump($method->invoke($obj= NULL));
     * </code>
     *
     * @param   lang.Object obj
     * @param   mixed[] args default array()
     * @return  mixed
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     */
    public function invoke($obj, $args= array()) {
      if (NULL !== $obj) {
        if (!is(xp::nameOf($this->_ref), $obj)) {
          throw new IllegalArgumentException(sprintf(
            'Passed argument is not a %s class (%s)',
            xp::nameOf($this->_ref),
            xp::typeOf($obj)
          ));
        }
      }

      try {
        if (!is_array($args)) $args= (array)$args;
        return $this->_reflect->invokeArgs($obj, $args);
      } catch (ReflectionException $e) {
        throw new XPException($e->getMessage());
      }
    }
  }
?>
