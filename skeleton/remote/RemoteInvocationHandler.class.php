<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.InvocationHandler');

  /**
   * Invocation handler for client stubs
   *
   * @see      xp://lang.reflect.InvocationHandler
   * @see      xp://RemoteInterfaceMapping
   * @purpose  InvocationHandler
   */
  class RemoteInvocationHandler extends Object implements InvocationHandler {
    public
      $oid      = NULL,
      $handler  = NULL;

    /**
     * Retrieve a new instance 
     *
     * @param   string oid
     * @param   ProtocolHandler handler
     * @return  RemoteInvocationHandler
     */
    public static function newInstance($oid, $handler) {
      with ($i= new RemoteInvocationHandler()); {
        $i->oid= $oid;
        $i->handler= $handler;
      }

      return $i;
    }
    
    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   var[] args an array of arguments
     * @return  var
     */
    public function invoke($proxy, $method, $args) { 
      return $this->handler->invoke($this->oid, $method, $args);
    }
  
  } 
?>
