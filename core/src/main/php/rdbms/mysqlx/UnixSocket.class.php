<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.mysqlx';

  uses('rdbms.mysqlx.LocalSocket', 'peer.BSDSocket');

  /**
   * Use an AF_UNIX socket. The socket's location is determined in the 
   * following way:
   * <ol>
   *   <li>First, the well-known locations /tmp/mysql.sock and /var/lib/mysql/mysql.sock 
   *       are checked, in that order
   *   </li>
   *   <li>Then, the environment variable named "MYSQL_UNIX_PORT" is tested</li>
   *   <li>Finally, the MySQL configuration file is looked for inside the current 
   *       user's home directory (<tt>~/.my.cnf</tt>) and then searched for in
   *       the directories /etc/ and /etc/mysql/ by name "my.cnf"
   *   </li>
   * </ol>
   *
   * @see   xp://rdbms.mysqlx.LocalSocket
   * @see   http://dev.mysql.com/doc/refman/5.1/en/problems-with-mysql-sock.html
   */
  class rdbms·mysqlx·UnixSocket extends LocalSocket {

    /**
     * Find local socket
     *
     * @return  string or NULL if no file can be found
     */
    protected function locate() {

      // 1. Check well-known locations, 2. environment
      foreach (array('/tmp/mysql.sock', '/var/lib/mysql/mysql.sock', getenv('MYSQL_UNIX_PORT')) as $file) {
        if (file_exists($file)) return $file;
      }

      // 3. Check config files
      foreach (array(getenv('HOME').'/.my.cnf', '/etc/my.cnf', '/etc/mysql/my.cnf') as $ini) {
        if (!file_exists($ini)) continue;
        $options= $this->parse(new File($ini));
        if (isset($options['client']['socket'])) return $options['client']['socket'];
      }

      return NULL;
    }

    /**
     * Creates the socket instance
     *
     * @param   string socket default NULL
     * @return  peer.Socket
     */
    public function newInstance($socket= NULL) {
      $sock= new BSDSocket(NULL === $socket ? $this->locate() : $socket, -1);
      $sock->setDomain(AF_UNIX);
      $sock->setType(SOCK_STREAM);
      $sock->setProtocol(0);
      return $sock;
    }
  }
?>
