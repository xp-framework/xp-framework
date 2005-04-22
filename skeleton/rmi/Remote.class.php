<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL', 
    'rmi.protocol.HandlerFactory', 
    'lang.reflect.Proxy',
    'rmi.RemoteContext'
  );

  /**
   * This class is the starting context for performing remote operations.
   *
   * <code>
   *   $remote= &Remote::forName('xp://jboss.example.com:4446/my/Bean');
   * </code>
   *
   * @purpose  RMI
   */
  class Remote extends Object {
  
    /**
     * Retrieve a remote object by name
     *
     * @model   static
     * @access  public
     * @param   string name
     * @return  &rmi.Remote
     * @param   &rmi.RemoteContext context default NULL
     */
    function &forName($name, $context= NULL) {
      return Remote::forURL(new URL($name), $context);
    }
    
    /**
     * Retrieve a remote object by URL
     *
     * @model   static
     * @access  public
     * @param   &peer.URL url
     * @param   &rmi.RemoteContext context default NULL
     * @return  &rmi.Remote
     */
    function &forURL(&$url, $context= NULL) {
      if (!$context) $context= &new RemoteContext(); // Empty context

      // Load the handler class and initialize it
      try(); {
        $handler= &HandlerFactory::factory($url);
        $interface= $handler->initializeFor($url, $context);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Create a proxy instance
      $cl= &ClassLoader::getDefault();
      try(); {
        $instance= &Proxy::newProxyInstance(
          $cl, 
          array($cl->loadClass($interface)),
          $handler
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    
      return $instance;
    }
  }
?>
