<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.unittest.rdbms.drivers.AbstractDriverTest');

  /**
   * Test driver for PostgreSQL connectivity is available
   *
   * @see      xp://rdbms.pgsql.PostgreSQLConnection
   * @purpose  Unit Test
   */
  class PostgreSQLDriverTest extends AbstractDriverTest {
  
    /**
     * Returns driver name
     *
     * @access  protected
     * @return  string
     */
    public function driverName() {
      return 'ext://pgsql';
    }
  }
?>
