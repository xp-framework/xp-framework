<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Local socket: If "localhost" is supplied as host name to a MySqlx
   * connection, a local socket is used depending on the operating system. 
   * To force using TCP/IP, use the value "127.0.0.1" instead.
   *
   * This exist for compatibility reasons with the MySQL client library!
   *
   * @see     http://dev.mysql.com/doc/refman/5.1/en/option-files.html
   */
  abstract class LocalSocket extends Object {

    /**
     * Returns the implementation for the given operating system.
     *
     * @param   string os operating system name, e.g. PHP_OS
     * @return  rdbms.mysqlx.LocalSocket
     */
    public static function forName($os) {
      if (0 === strncasecmp($os, 'Win', 3)) {
        return XPClass::forName('rdbms.mysqlx.NamedPipe')->newInstance();
      } else {
        return XPClass::forName('rdbms.mysqlx.UnixSocket')->newInstance();
      }
    }

    /**
     * Parse my.cnf file and return sections
     *
     * @param   io.File ini
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
     * @return  peer.Socket
     */
    public abstract function newInstance();
  }
?>
