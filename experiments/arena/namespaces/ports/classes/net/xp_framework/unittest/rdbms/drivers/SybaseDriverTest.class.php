<?php
/* This class is part of the XP framework
 *
 * $Id: SybaseDriverTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::rdbms::drivers;
 
  ::uses('net.xp_framework.unittest.rdbms.drivers.AbstractDriverTest');

  /**
   * Test driver for Sybase connectivity is available
   *
   * @see      xp://rdbms.sybase.SybaseConnection
   * @purpose  Unit Test
   */
  class SybaseDriverTest extends AbstractDriverTest {
  
    /**
     * Returns driver name
     *
     * @return  string
     */
    public function driverName() {
      return 'ext://sybase_ct';
    }
  }
?>
