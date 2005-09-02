<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Proxy');

  /**
   * Maps serialized representation of remote interface to a Proxy 
   * instance.
   *
   * @see      xp://RemoteInvocationHandler
   * @see      xp://Serializer
   * @purpose  Serializer mapping
   */
  class RemoteInterfaceMapping extends Object {
  
    /**
     * Returns a value for the given serialized string
     *
     * @access  public
     * @param   string serialized
     * @param   &int length
     * @param   array<string, mixed> context default array()
     * @return  &mixed
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

  } implements(__FILE__, 'SerializerMapping');
?>
