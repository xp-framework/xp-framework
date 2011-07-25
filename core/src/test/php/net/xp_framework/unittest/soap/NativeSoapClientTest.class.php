<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */

  uses(
    'unittest.TestCase',
    'webservices.soap.native.NativeSoapClient'
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
      $client= new NativeSoapClient('http://localhost:12345', 'uri');
      $client->setTimeout(2);
      $client->setConnectTimeout(2);
      $client->invoke('foo', 'bar');
    }

  }
?>
