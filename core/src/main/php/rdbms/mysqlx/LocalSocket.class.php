<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Local socket: If "." is supplied as host name to a MySqlx connection,
   * a local socket is used depending on the operating system. 
   *
   * Note:
   * This is different from the MySQL client libraries! They use local 
   * sockets if "localhost" is supplied and can only be forced to use
   * TCP/IP by suppliying the value "127.0.0.1" instead.
   *
   * @see     http://dev.mysql.com/doc/refman/5.1/en/option-files.html
   */
  abstract class LocalSocket extends Object {

    /**
     * Returns the implementation for the given operating system.
     *
     * @param   string os operating system name, e.g. PHP_OS
     * @param   string socket default NULL
     * @return  rdbms.mysqlx.LocalSocket
     */
    public static function forName($os, $socket= NULL) {
      if (0 === strncasecmp($os, 'Win', 3)) {
        return XPClass::forName('rdbms.mysqlx.NamedPipe')->newInstance($socket);
      } else {
        return XPClass::forName('rdbms.mysqlx.UnixSocket')->newInstance($socket);
      }
    }

    /**
     * Parse my.cnf file and return sections
     *
     * @param   io.File cnf
     * @return  [:[:string]] sections
     */
    protected function parse($cnf) {
      $cnf->open(FILE_MODE_READ);
      $section= NULL;
      $sections= array();
      while (FALSE !== ($line= $cnf->readLine())) {
        if ('' === $line || '#' === $line{0}) {
          continue;
        } else if ('[' === $line{0}) {
          $section= strtolower(trim($line, '[]'));
        } else if (FALSE !== ($p= strpos($line, '='))) {
          $key= strtolower(trim(substr($line, 0, $p)));
          $value= trim(substr($line, $p+ 1));
          $sections[$section][$key]= $value;
        }
      }
      $cnf->close();
      return $sections;
    }

    /**
     * Creates the socket instance
     *
     * @param   string socket default NULL
     * @return  peer.Socket
     */
    public abstract function newInstance($socket= NULL);
  }
?>
