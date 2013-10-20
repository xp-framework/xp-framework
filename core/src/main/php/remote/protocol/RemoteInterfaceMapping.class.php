<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.reflect.Proxy',
    'remote.RemoteInvocationHandler',
    'remote.protocol.SerializerMapping',
    'remote.server.RemoteObjectMap'
  );

  /**
   * Maps serialized representation of remote interface to a Proxy 
   * instance.
   *
   * @see      xp://remote.RemoteInvocationHandler
   * @see      xp://remote.protocol.Serializer
   * @purpose  Serializer mapping
   */
  class RemoteInterfaceMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $oid= $serialized->consumeSize();
      $serialized->consume('{');
      $interface= $serializer->valueOf($serialized, $context);
      $serialized->consume('}');

      return Proxy::newProxyInstance(
        ClassLoader::getDefault(), 
        array(XPClass::forName($serializer->packageMapping($interface))), 
        RemoteInvocationHandler::newInstance((int)$oid, $context['handler'])
      );
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   [:var] context default array()
     * @return  string
     */
    public function representationOf($serializer, $var, $context= array()) {

      // Fetch OID for the given object      
      $oid= $context[RemoteObjectMap::CTX_KEY]->oidFor($var);
      
      // Find home interface
      foreach (($interfaces= $var->getClass()->getInterfaces()) as $interface) {
        if ($interface->isSubclassOf('remote.beans.BeanInterface')) {
          return 'I:'.$oid.':{'.$serializer->representationOf($interface->getName(), $context).'}';
        }
      }
      
      throw new IllegalArgumentException('Not a BeanInterface: '.$var->getClassName());
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return XPClass::forName('lang.reflect.Proxy');
    }
  } 
?>
