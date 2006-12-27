<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.unittest.rdbms.drivers.AbstractDriverTest');

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
