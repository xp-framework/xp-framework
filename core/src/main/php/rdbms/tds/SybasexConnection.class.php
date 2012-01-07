<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.tds.TdsConnection',
    'rdbms.tds.TdsV5Protocol',
    'rdbms.tds.ConnectionLookup',
    'rdbms.sybase.SybaseDialect'
  );

  /**
   * Connection to Sybase Databases via TDS 5.0
   *
   * @see   xp://rdbms.tds.TdsConnection
   */
  class SybasexConnection extends TdsConnection {
    protected static $lookup;

    static function __static() {
      if (strncasecmp(PHP_OS, 'Win', 3) === 0) {
        self::$lookup= XPClass::forName('rdbms.tds.SqlIniLookup')->newInstance();
      } else if (getenv('SYBASE')) {
        self::$lookup= XPClass::forName('rdbms.tds.InterfacesLookup')->newInstance();
      } else {
        self::$lookup= XPClass::forName('rdbms.tds.FreeTdsLookup')->newInstance();
      }
      DriverManager::register('sybase+x', new XPClass(__CLASS__));
    }

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) {
      if (NULL === $dsn->getPort(NULL)) {       // Check lookup
        self::$lookup->lookup($dsn);
      }
      parent::__construct($dsn);
    }

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
