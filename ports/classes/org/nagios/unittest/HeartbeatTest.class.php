<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'org.nagios.nsca.Heartbeat'
  );

  /**
   * Test heartbeat class
   *
   * @see      xp://org.nagios.nsca.Heartbeat
   * @purpose  TestCase
   */
  class HeartbeatTest extends TestCase {
    
    /**
     * Check that the hostname is calculated correctly
     *
     */
    #[@test]
    public function domainSuffix() {
      $beat= new Heartbeat();
      $beat->setup('nagios://nagios.xp-framework.net:5667/servicename?hostname=client&domain=.xp-framework.net');
      $this->assertEquals('client.xp-framework.net', $beat->host);

      $beat->setup('nagios://nagios.xp-framework.net:5667/servicename?hostname=client&domain=xp-framework.net');
      $this->assertEquals('client.xp-framework.net', $beat->host);
    }
  }
?>
