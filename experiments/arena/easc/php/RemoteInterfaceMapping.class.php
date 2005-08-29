<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class RemoteInterfaceMapping extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &valueOf($serialized, &$length, $context= array()) {
      $oid= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
      $offset= 2 + 2 + strlen($oid);
      $interface= Serializer::valueOf(substr($serialized, $offset), $len, $handler);

      $cl= &ClassLoader::getDefault();
      try(); {
        $instance= &Proxy::newProxyInstance(
          $cl, 
          array(XPClass::forName($interface, $cl)), 
          RemoteInvocationHandler::newInstance((int)$oid, $context['handler'])
        );
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      $length= $offset+ 1;
      return $instance;
    }
  }
?>
