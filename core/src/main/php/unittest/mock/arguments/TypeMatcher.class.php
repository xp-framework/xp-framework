<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.mock.arguments.IArgumentMatcher',
    'lang.reflect.InvocationHandler'
  );

  /**
   * Argument matcher based on argument type
   *
   * @test  xp://net.xp_framework.unittest.tests.mock.TypeMatcherTest
   */
  class TypeMatcher extends Object implements IArgumentMatcher, InvocationHandler  {
    private 
      $type,
      $matchNull;
    
    /**
     * Constructor.
     * 
     * @param   string type
     * @param   bool matchNull default TRUE
     */
    public function __construct($type, $matchNull= TRUE) {
      $this->type= $type;
      $this->matchNull= $matchNull;
    }
    
    /**
     * Matches implementation
     * 
     * @param   var value
     * @return  bool
     */
    public function matches($value) {
      if (NULL === $value && $this->matchNull) {
        return TRUE;
      }
      
      return xp::typeof($value) == XPClass::forName($this->type)->getName();
    }

    /**
     * Invocation handler
     *
     * @param   lang.reflect.Proxy
     * @param   string method
     * @param   var[] args
     * @return  var
     */
    public function invoke($proxy, $method, $args) {
      if ('matches' === $method) {
        return $this->matches($args[0]);
      }
      
      throw new IllegalStateException('Unknown method "'.$method.'".');
    }
  }
?>
