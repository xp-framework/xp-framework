<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.tds.TdsConnection',
    'rdbms.tds.TdsV7Protocol',
    'rdbms.mssql.MsSQLDialect'
  );

  /**
   * Connection to MSSQL Databases via TDS 7.0
   *
   * @see   xp://rdbms.tds.TdsConnection
   */
  class MsSQLxConnection extends TdsConnection {
    
    /**
     * Returns dialect
     *
     * @return  rdbms.SQLDialect
     */
    protected function getDialect() {
      return new MsSQLDialect();
    }
    
    /**
     * Returns protocol
     *
     * @param   peer.Socket sock
     * @return  rdbms.tds.TdsProtocol
     */
    protected function getProtocol($sock) {
      return new TdsV7Protocol($sock);
    }
  }
?>
