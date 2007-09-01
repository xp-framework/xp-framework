<?php
/* This class is part of the XP framework
 *
 * $Id: Constructor.class.php 10968 2007-08-27 16:58:55Z friebe $ 
 */

  namespace lang::reflect;

  ::uses('lang.reflect.Routine');

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
     * @param   mixed ref
     */    
    public function __construct($ref) {
      parent::__construct($ref, '__construct');
    }
    
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
        throw new lang::IllegalAccessException('Cannot instantiate abstract class '.$this->_ref);
      }

      // Check modifers
      $m= $this->_reflect->getModifiers();
      if (!($m & MODIFIER_PUBLIC) || $m & MODIFIER_ABSTRACT) {
        throw new lang::IllegalAccessException(sprintf(
          'Cannot invoke %s constructor of class %s',
          Modifiers::stringOf($this->getModifiers()),
          $this->_ref
        ));
      }

      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      try {
        return eval('return new '.$this->_ref.'('.substr($paramstr, 2).');');
      } catch (lang::Throwable $e) {
        throw new TargetInvocationException($this->_ref.'::<init>', $e);
      }
    }

    /**
     * Retrieve return type
     *
     * @return  string
     */
    public function getReturnType() {
      return ::xp::nameOf($this->_ref);
    }
  }
?>
