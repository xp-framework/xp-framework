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
     * Uses the constructor represented by this Constructor object to create 
     * and initialize a new instance of the constructor's declaring class, 
     * with the specified initialization parameters.
     *
     * Example:
     * <code>
     *   $constructor= XPClass::forName('utl.Binford')->getConstructor();
     *
     *   var_dump($constructor->newInstance());
     * </code>
     *
     * @param   var[] args
     * @return  lang.Generic
     * @throws  lang.reflect.TargetInvocationException
     * @throws  lang.IllegalAccessException in case the constructor is not public or if it is abstract
     * @throws  lang.reflect.TargetInvocationException in case the constructor throws an exception
     */
    public function newInstance(array $args= array()) {

      // Check whether class is abstract
      $class= new ReflectionClass($this->_class);
      if ($class->isAbstract()) {
        throw new IllegalAccessException('Cannot instantiate abstract class '.$this->_class);
      }

      // Check modifers
      $m= $this->_reflect->getModifiers();
      if (!($m & MODIFIER_PUBLIC)) {
        throw new IllegalAccessException(sprintf(
          'Cannot invoke %s constructor of class %s',
          Modifiers::stringOf($this->getModifiers()),
          $this->_class
        ));
      }
      
      $paramstr= '';
      for ($i= 0, $m= sizeof($args); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      try {
        return eval('return new '.$this->_class.'('.substr($paramstr, 2).');');
      } catch (Throwable $e) {
        throw new TargetInvocationException($this->_class.'::<init>', $e);
      }
    }

    /**
     * Retrieve return type
     *
     * @return  lang.Type
     */
    public function getReturnType() {
      return XPClass::forName(xp::nameOf($this->_class));
    }

    /**
     * Retrieve return type
     *
     * @return  string
     */
    public function getReturnTypeName() {
      return xp::nameOf($this->_class);
    }
  }
?>
