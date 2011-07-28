<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Factory for HTTP response adapters
   *
   * @test    xp://net.xp_framework.unittest.rest.transport.HttpResponseAdapterFactoryTest
   * @purpose Factory
   */
  class HttpResponseAdapterFactory extends Object {
    
    /**
     * Create adapter for response based on accept header
     * 
     * @param scriptlet.HttpScriptletRequest request The request
     * @param scriptlet.HttpScriptletResponse response The response
     * @return lang.XPClass
     */
    public static function forRequest($request) {
      static $map= array(
        'application/json' => 'webservices.rest.transport.JsonHttpResponseAdapter'
      );
      
      if (!isset($map[$request->getHeader('Accept')])) throw new IllegalArgumentException(
        'The accept type is not supported: '.$request->getHeader('Accept')
      );
      
      return XPClass::forName($map[$request->getHeader('Accept')]);
    }
  }
?>
