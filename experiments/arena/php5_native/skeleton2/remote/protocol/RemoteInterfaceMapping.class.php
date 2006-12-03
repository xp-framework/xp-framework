<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.reflect.Proxy',
    'remote.RemoteInvocationHandler',
    'remote.protocol.SerializerMapping'
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
  class RemoteInterfaceMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   &remote.protocol.SerializedData serialized
     * @param   array<string, mixed> context default array()
     * @return  &mixed
     */
    public function &valueOf(&$serializer, &$serialized, $context= array()) {
      $oid= $serialized->consumeSize();
      $serialized->offset++;    // '{'
      $interface= $serializer->valueOf($serialized, $context);
      $serialized->offset++;    // '}'
      try {
        $iclass= &XPClass::forName($interface);
      } catch (ClassNotFoundException $e) {
        throw($e);
      }

      $cl= &ClassLoader::getDefault();      
      try {
        $instance= &Proxy::newProxyInstance(
          $cl, 
          array($iclass), 
          RemoteInvocationHandler::newInstance((int)$oid, $context['handler'])
        );
      } catch (ClassNotFoundException $e) {
        throw($e);
      }

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
    public function representationOf(&$serializer, &$var, $context= array()) {
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
    public function &handledClass() {
      return XPClass::forName('lang.reflect.Proxy');
    }
  } 
?>
