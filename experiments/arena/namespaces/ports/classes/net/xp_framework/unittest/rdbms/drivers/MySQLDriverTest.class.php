<?php
/* This class is part of the XP framework
 *
 * $Id: MySQLDriverTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::rdbms::drivers;
 
  ::uses('net.xp_framework.unittest.rdbms.drivers.AbstractDriverTest');

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
     * @return  string
     */
    public function driverName() {
      return 'ext://mysql';
    }
  }
?>
