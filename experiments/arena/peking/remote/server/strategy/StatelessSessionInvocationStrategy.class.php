<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.reflect.InterfaceUtil',
    'lang.reflect.Proxy'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class StatelessSessionInvocationStrategy extends Object {
    var
      $poolSize = 1;
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function invokeHome(&$container, $method, $args) {
      /*if ($container->instancePool->size() >= $this->poolSize) {
        return Proxy::newProxyInstance(
          ClassLoader::getDefault(),
          InterfaceUtil::getUniqueInterfacesFor($instance->getClass()),
          $invocationHandler
        );
      }*/
    
      $instance= &$container->instancePool->add($container->peer->{$method}());
      $oid= $container->instancePool->indexOf($instance);
      
      $invocationHandler= &new ContainerInvocationHandler();
      $invocationHandler->setContainer($container);
      $invocationHandler->setType(INVOCATION_TYPE_BEAN);
      $invocationHandler->setOID($oid);

      return Proxy::newProxyInstance(
        ClassLoader::getDefault(),
        InterfaceUtil::getUniqueInterfacesFor($instance->getClass()),
        $invocationHandler
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function invoke($container, $oid, $method, $args) {
      $invokeable= &$container->instancePool->get($oid);
      if (!is('lang.Object', $invokeable)) return throw(new RemoteException(
        'No valid invokeable found for oid '.$oid
      ));
      
      $class= &$invokeable->getClass();
      $m= &$class->getMethod($method);
      
      if (!$m) return throw(new RemoteException(
        'No such method '.$method.' for oid '.$oid
      ));
      
      $ret= $m->invoke($invokeable, $args);
      return $ret;
    }
  }
?>
