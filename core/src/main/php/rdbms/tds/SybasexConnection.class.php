<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.tds.TdsConnection',
    'rdbms.tds.TdsV5Protocol',
    'rdbms.sybase.SybaseDialect',
    'io.File'
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
        self::$lookup= 'useSqlIni';
      } else if (getenv('SYBASE')) {
        self::$lookup= 'useInterfaces';
      } else {
        self::$lookup= 'useFreeTds';
      }
    }

    /**
     * Parse an ini file
     *
     * @param   string ini
     * @return  [:[:string]] sections
     */
    protected function parseIni($ini) {
      $f= new File($ini);
      $f->open(FILE_MODE_READ);
      $section= NULL;
      $sections= array();
      while (FALSE !== ($line= $f->readLine())) {
        if ('' === $line || ';' === $line{0}) {
          continue;
        } else if ('[' === $line{0}) {
          $section= strtolower(trim($line, '[]'));
        } else if (FALSE !== ($p= strpos($line, '='))) {
          $key= trim(substr($line, 0, $p));
          $value= trim(substr($line, $p+ 1));
          $sections[$section][$key]= $value;
        }
      }
      $f->close();
      return $sections;
    }

    /**
     * Use sql.ini file if possible
     *
     * @param   rdbms.DSN dsn
     */
    protected function useSqlIni($dsn) {
      if (!file_exists($ini= getenv('SYBASE').'\\ini\\sql.ini')) return;

      $host= strtolower($dsn->getHost());
      $sections= $this->parseIni($ini);
      if (!isset($sections[$host])) return;

      sscanf($sections[$host]['query'], '%[^,],%[^,],%d', $proto, $host, $port);
      $dsn->url->setHost(gethostbyname($host));
      $dsn->url->setPort($port);
    }

    /**
     * Use Sybase interfaces file if possible
     *
     * @param   rdbms.DSN dsn
     */
    protected function useInterfaces($dsn) {
      // TODO
    }

    /**
     * Use FreeTDS config file if possible
     *
     * @param   rdbms.DSN dsn
     */
    protected function useFreeTds($dsn) {
      // TODO
    }

    /**
     * Constructor
     *
     * @param   rdbms.DSN dsn
     */
    public function __construct($dsn) {
      if (NULL === $dsn->getPort(NULL)) {       // Check lookup
        $this->{self::$lookup}($dsn);
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
