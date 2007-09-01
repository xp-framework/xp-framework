<?php
/* This class is part of the XP framework
 *
 * $Id: RemoteInvocationHandler.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote;

  uses('lang.reflect.InvocationHandler');

  /**
   * Invocation handler for client stubs
   *
   * @see      xp://lang.reflect.InvocationHandler
   * @see      xp://RemoteInterfaceMapping
   * @purpose  InvocationHandler
   */
  class RemoteInvocationHandler extends lang::Object implements lang::reflect::InvocationHandler {
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
     * @param   mixed* args an array of arguments
     * @return  mixed
     */
    public function invoke($proxy, $method, $args) { 
      return $this->handler->invoke($this->oid, $method, $args);
    }
  
  } 
?>
