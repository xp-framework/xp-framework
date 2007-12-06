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
     * @param   mixed* args
     * @return  lang.Object
     * @throws  lang.IllegalAccessException in case the constructor is not public or if it is abstract
     */
    public function newInstance() {

      // Check whether class is abstract
      if ($this->_reflect->getDeclaringClass()->isAbstract()) {
        throw new IllegalAccessException('Cannot instantiate abstract class '.$this->_ref);
      }

      // Check modifers
      $m= $this->_reflect->getModifiers();
      if (!($m & MODIFIER_PUBLIC) || $m & MODIFIER_ABSTRACT) {
        throw new IllegalAccessException(sprintf(
          'Cannot invoke %s constructor of class %s',
          Modifiers::stringOf($this->getModifiers()),
          $this->_class
        ));
      }

      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
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
     * @return  string
     */
    public function getReturnType() {
      return xp::nameOf($this->_class);
    }
  }
?>
