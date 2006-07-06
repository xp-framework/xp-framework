<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.reflect.Proxy',
    'remote.RemoteInvocationHandler'
  );

  /**
   * Maps serialized representation of remote interface to a Proxy 
   * instance.
   *
   * @see      xp://remote.RemoteInvocationHandler
   * @see      xp://remote.protocol.Serializer
   * @purpose  Serializer mapping
   */
  class RemoteInterfaceMapping extends Object {
  
    /**
     * Returns a value for the given serialized string
     *
     * @access  public
     * @param   &remote.protocol.Serializer serializer
     * @param   string serialized
     * @param   &int length
     * @param   array<string, mixed> context default array()
     * @return  &mixed
     */
    function &valueOf(&$serializer, $serialized, &$length, $context= array()) {
      $oid= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
      $offset= 2 + 2 + strlen($oid);
      $interface= $serializer->valueOf(substr($serialized, $offset), $len, $context);
      $offset+= $len;

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

  } implements(__FILE__, 'remote.protocol.SerializerMapping');
?>
