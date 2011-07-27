<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */

  uses(
    'unittest.TestCase',
    'lang.types.Long',
    'webservices.soap.native.NativeSoapClient',
    'webservices.soap.types.SOAPLong',
    'webservices.soap.types.SOAPDouble'
  );

  /**
   * TestCase
   *
   * @see       ...
   * @purpose   TestCase for
   */
  class NativeSoapClientTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function test() {
      $client= new NativeSoapClient('http://127.0.0.1:12345/', 'foo');
      $client->setTimeout(2);
      $client->setConnectTimeout(2);
      $client->invoke('testMethod',
        new SOAPLong('99999999'),
        new Long('88888888'),
        new SOAPDouble(15.5),
        new Double(12.5)
      );
    }

  }
?>
