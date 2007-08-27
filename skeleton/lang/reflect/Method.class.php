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
     *   $method= XPClass::forName('lang.types.String')->getMethod('concat');
     *
     *   var_dump($method->invoke(new String('Hello'), array('World')));
     * </code>
     *
     * Example (static invokation):
     * <code>
     *   $method= XPClass::forName('util.log.Logger')->getMethod('getInstance');
     *
     *   var_dump($method->invoke($obj= NULL));
     * </code>
     *
     * @param   lang.Object obj
     * @param   mixed[] args default array()
     * @return  mixed
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     * @throws  lang.IllegalAccessException in case the method is not public or if it is abstract
     */
    public function invoke($obj, $args= array()) {
      if (NULL !== $obj && !($obj instanceof $this->_ref)) {
        throw new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_ref),
          xp::typeOf($obj)
        ));
      }
      
      // Check modifers
      $m= $this->_reflect->getModifiers();
      if (!($m & MODIFIER_PUBLIC) || $m & MODIFIER_ABSTRACT) {
        throw new IllegalAccessException(sprintf(
          'Cannot invoke %s %s::%s',
          Modifiers::stringOf($this->getModifiers()),
          $this->_ref,
          $this->name
        ));
      }

      try {
        return $this->_reflect->invokeArgs($obj, (array)$args);
      } catch (Throwable $e) {
        throw new TargetInvocationException($this->_ref.'::'.$this->name.'() ~ '.$e->getMessage(), $e);
      } catch (ReflectionException $e) {

        // This should never occur, we checked everything beforehand...
        throw new TargetInvocationException($this->_ref.'::'.$this->name.'() ~ '.$e->getMessage(), new XPException($e->getMessage()));
      }
    }
  }
?>
