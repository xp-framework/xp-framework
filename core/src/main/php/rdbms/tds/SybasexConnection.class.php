<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.tds.TdsConnection',
    'rdbms.tds.TdsV5Protocol',
    'rdbms.sybase.SybaseDialect'
  );

  /**
   * Connection to MSSQL Databases via TDS
   *
   * @see   xp://rdbms.tds.TdsConnection
   */
  class SybasexConnection extends TdsConnection {
    
    /**
     * Returns dialect
     *
     * @return  rdbms.SQLDialect
     */
    protected function getDialect() {
      return new SybaseDialect();
    }
    
    /**
     * Returns protocol
     *
     * @param   peer.Socket sock
     * @return  rdbms.tds.TdsProtocol
     */
    protected function getProtocol($sock) {
      return new TdsV5Protocol($sock);
    }
  }
?>
