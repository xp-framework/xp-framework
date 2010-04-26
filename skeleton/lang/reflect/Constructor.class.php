<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Routine');

  /**
   * Represents a class' constructor
   *
   * @see   xp://lang.XPClass
   * @see   xp://lang.reflect.Routine
   * @test  xp://net.xp_framework.unittest.reflection.ReflectionTest
   */
  class Constructor extends Routine {

    /**
     * Uses the constructor represented by this Constructor object to create 
     * and initialize a new instance of the constructor's declaring class, 
     * with the specified initialization parameters.
     *
     * Example:
     * <code>
     *   $constructor= XPClass::forName('util.Binford')->getConstructor();
     *
     *   $instance= $constructor->newInstance();
     *   $instance= $constructor->newInstance(array(6100));
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

      // Check modifiers. If caller is an instance of this class, allow
      // protected constructor invocation (which the PHP reflection API
      // does not).
      $m= $this->_reflect->getModifiers();
      if (!($m & MODIFIER_PUBLIC)) {
        if (!$this->accessible) {
          $t= debug_backtrace();
          if ($t[1]['class'] !== $this->_class) {
            $scope= new ReflectionClass($t[1]['class']);
            if (!$scope->isSubclassOf($this->_class)) {
              throw new IllegalAccessException(sprintf(
                'Cannot invoke %s constructor of class %s from scope %s',
                Modifiers::stringOf($this->getModifiers()),
                $this->_class,
                $t[1]['class']
              ));
            }
          }
        }
        
        // Create instance without invoking constructor
        $instance= unserialize('O:'.strlen($this->_class).':"'.$this->_class.'":0:{}');
        $inv= '$instance->{"\7__construct"}(%s); return $instance;';
      } else {
        $inv= 'return new '.$this->_class.'(%s);';
      }
      
      $paramstr= '';
      for ($i= 0, $m= sizeof($args); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      try {
        return eval(sprintf($inv, substr($paramstr, 2)));
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
