<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.ConnectionLookup', 'io.File');

  /**
   * Lookup host name and port to connect to by Interfaces.file file
   *
   * @test    xp://net.xp_framework.unittest.rdbms.tds.InterfacesLookupTest
   */
  class InterfacesLookup extends Object implements rdbms·tds·ConnectionLookup {
    protected $file= NULL;
    
    /**
     * Creates a new sql.file lookup instance with a given file. If
     * the file is omitted, ENV{SYBASE}/interfaces is used.
     *
     * @param   io.File file
     */
    public function __construct($file= NULL) {
      $this->file= NULL === $file
        ? new File(getenv('SYBASE').'/interfaces')
        : $file
      ;
    }
    
    /**
     * Parse interface file and return sections
     *
     * @return  [:[:string]] sections
     */
    protected function parse() {
      $this->file->open(FILE_MODE_READ);
      $section= NULL;
      $sections= array();
      while (FALSE !== ($line= $this->file->readLine())) {
        if ('' === $line || '#' === $line{0}) {
          continue;
        } else if (' ' === $line{0} || "\t" === $line{0}) {
          sscanf($line, "%*[ \t]%s %[^\r]", $key, $value);
          $sections[$section][$key]= $value;
        } else {
          $section= strtolower(trim($line, '[]'));
        }
      }
      $this->file->close();
      return $sections;
    }

    /**
     * Look up DSN. Reparses fileig file every time its called.
     *
     * @param   rdbms.DSN dsn
     */
    public function lookup($dsn) {
      if (!$this->file->exists()) return;

      $host= strtolower($dsn->getHost());
      $sections= $this->parse();
      if (!isset($sections[$host]['query'])) return;

      sscanf($sections[$host]['query'], '%s %*s %s %d', $proto, $host, $port);
      $dsn->url->setHost($host);
      $dsn->url->setPort($port);
    }
  }
?>
