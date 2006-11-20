<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.unittest.rdbms.drivers.AbstractDriverTest');

  /**
   * Test driver for MySQL connectivity is available
   *
   * @see      xp://rdbms.mysql.MySQLConnection
   * @purpose  Unit Test
   */
  class MySQLDriverTest extends AbstractDriverTest {
  
    /**
     * Returns driver name
     *
     * @access  protected
     * @return  string
     */
    function driverName() {
      return 'ext://mysql';
    }
  }
?>
