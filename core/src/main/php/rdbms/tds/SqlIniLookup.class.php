<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.ConnectionLookup', 'io.File');

  /**
   * Lookup host name and port to connect to by sql.ini file
   *
   * @test    xp://net.xp_framework.unittest.rdbms.tds.SqlIniLookupTest
   */
  class SqlIniLookup extends Object implements rdbms·tds·ConnectionLookup {
    protected $ini= NULL;
    
    /**
     * Creates a new sql.ini lookup instance with a given file. If
     * the file is omitted, ENV{SYBASE}/ini/sql.ini is used.
     *
     * @param   io.File ini
     */
    public function __construct($ini= NULL) {
      $this->ini= NULL === $ini
        ? new File(getenv('SYBASE').'/ini/sql.ini')
        : $ini
      ;
    }
    
    /**
     * Parse sql.ini file and return sections
     *
     * @return  [:[:string]] sections
     */
    protected function parse() {
      $this->ini->open(FILE_MODE_READ);
      $section= NULL;
      $sections= array();
      while (FALSE !== ($line= $this->ini->readLine())) {
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
      $this->ini->close();
      return $sections;
    }

    /**
     * Look up DSN. Reparses SQL.ini file every time its called.
     *
     * @param   rdbms.DSN dsn
     */
    public function lookup($dsn) {
      if (!$this->ini->exists()) return;

      $host= strtolower($dsn->getHost());
      $sections= $this->parse();
      if (!isset($sections[$host]['query'])) return;

      sscanf($sections[$host]['query'], '%[^,],%[^,],%d', $proto, $host, $port);
      if (strstr($host, ':')) {
        $dsn->url->setHost('['.$host.']');
      } else {
        $dsn->url->setHost($host);
      }
      $dsn->url->setPort($port);
    }
  }
?>
