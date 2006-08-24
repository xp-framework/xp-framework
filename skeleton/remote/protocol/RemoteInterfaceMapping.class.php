<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.reflect.Proxy',
    'remote.RemoteInvocationHandler'
  );

  // Defines for context keys
  define('RIH_OBJECTS_KEY',       'objects');
  define('RIH_OIDS_KEY',          'oids');

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

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   &lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    function representationOf(&$serializer, &$var, $context= array()) {
      static $oid=  0;
      
      // Check if we've serialized this object before by looking it up
      // through the hashCode() method. If not, remember that we did for
      // the next run.
      if (!$context[RIH_OIDS_KEY]->containsKey($var->hashCode())) {
        $context[RIH_OBJECTS_KEY]->putref($oid, $var);
        $context[RIH_OIDS_KEY]->put($var->hashCode(), $oid);
        $oid++;
      }
      
      // Fetch the stored "external" OID
      $eoid= $context[RIH_OIDS_KEY]->get($var->hashCode());
      
      // Find home interface
      $class= &$var->getClass();
      $homeInterface= NULL;
      
      foreach (($interfaces= &$class->getInterfaces()) as $interface) {
        if ($interface->isSubclassOf('remote.beans.BeanInterface')) {
          $homeInterface= &$interface;
          break;
        }
      }
      
      $name= $homeInterface->getName();
      return 'I:'.$eoid.':{'.$serializer->representationOf($name, $context).'}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &handledClass() {
      return XPClass::forName('lang.reflect.Proxy');
    }
  } implements(__FILE__, 'remote.protocol.SerializerMapping');
?>
