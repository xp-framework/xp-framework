<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.ConnectionLookup', 'io.File');

  /**
   * Lookup host name and port to connect to by sql.ini file
   *
   *
   */
  class SqlIniLookup extends Object implements rdbms·tds·ConnectionLookup {
    protected $ini= NULL;
    
    /**
     * Creates a new 
     *
     * @param   io.File ini
     */
    public function __construct($ini= NULL) {
      $this->ini= NULL === $ini
        ? new File((self::$env ? self::$env : getenv('SYBASE')).'\\ini\\sql.ini')
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
     * Look up DSN
     *
     * @param   rdbms.DSN dsn
     */
    public function lookup($dsn) {
      if (!$this->ini->exists()) return;

      $host= strtolower($dsn->getHost());
      $sections= $this->parse();
      if (!isset($sections[$host])) return;

      sscanf($sections[$host]['query'], '%[^,],%[^,],%d', $proto, $host, $port);
      $dsn->url->setHost(gethostbyname($host));
      $dsn->url->setPort($port);
    }
  }
?>
