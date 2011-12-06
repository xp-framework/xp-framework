<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.ConnectionLookup', 'io.File');

  /**
   * Lookup host name and port to connect to by freetds.conf file
   *
   * @test    xp://net.xp_framework.unittest.rdbms.tds.FreeTdsLookupTest
   */
  class FreeTdsLookup extends Object implements rdbms·tds·ConnectionLookup {
    protected $conf= NULL;
    
    /**
     * Creates a new sql.conf lookup instance with a given file. If
     * the file is omitted, /etc/freetds/freetds.conf is used.
     *
     * @param   io.File conf
     */
    public function __construct($conf= NULL) {
      $this->conf= NULL === $conf
        ? new File('/etc/freetds/freetds.conf')
        : $conf
      ;
    }
    
    /**
     * Parse freetds.conf file and return sections
     *
     * @return  [:[:string]] sections
     */
    protected function parse() {
      $this->conf->open(FILE_MODE_READ);
      $section= NULL;
      $sections= array();
      while (FALSE !== ($line= $this->conf->readLine())) {
        $line= trim($line);
        if ('' === $line || ';' === $line{0} || '#' === $line{0}) {
          continue;
        } else if ('[' === $line{0}) {
          $section= strtolower(trim($line, '[]'));
        } else if (FALSE !== ($p= strpos($line, '='))) {
          $key= trim(substr($line, 0, $p));
          $value= trim(substr($line, $p+ 1));
          $sections[$section][$key]= $value;
        }
      }
      $this->conf->close();
      return $sections;
    }

    /**
     * Look up DSN. Reparses config file every time its called.
     *
     * @param   rdbms.DSN dsn
     */
    public function lookup($dsn) {
      if (!$this->conf->exists()) return;

      $host= strtolower($dsn->getHost());
      $sections= $this->parse();
      if (!isset($sections[$host])) return;

      $dsn->url->setHost($sections[$host]['host']);
      $dsn->url->setPort((int)$sections[$host]['port']);
    }
  }
?>
