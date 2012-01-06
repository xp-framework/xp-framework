<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.mysqlx';

  uses('rdbms.mysqlx.LocalSocket', 'io.File');

  /**
   * Use a named pipe. Determines the pipe's name by checking for "mysql"
   * and then by parsing my.ini "client.socket" setting.
   *
   * @see   http://dev.mysql.com/doc/refman/5.1/en/option-files.html
   * @see   xp://rdbms.mysqlx.LocalSocket
   */
  class rdbms·mysqlx·NamedPipe extends LocalSocket {

    /**
     * Find named pipe
     *
     * @return  string or NULL if no file can be found
     */
    protected function locate() {
      $pipes= '\\\\.\\pipe\\';

      // Check well-known pipe name
      if (file_exists($pipes.'mysql')) return $pipes.'mysql';

      // Locate my.ini in %WINDIR%, C: or the MySQL install dir, the latter of
      // which we determine by querying the registry using the "REG" tool.
      do {
        foreach (array(getenv('WINDIR'), 'C:') as $location) {
          $ini= new File($location, 'my.ini');
          if ($ini->exists()) break 2;
        }

        exec('reg query "HKLM\SOFTWARE\MySQL AB" /s /e /f Location', $out, $ret);
        if (0 === $ret && 1 === sscanf($out[2], "    Location    REG_SZ    %[^\r]", $location)) {
          $ini= new File($location, 'my.ini');
          break;
        }
        
        return NULL;
      } while (0);

      $options= $this->parse($ini);
      return isset($options['client']['socket']) ? $pipes.$options['client']['socket'] : NULL;
    }
    
    /**
     * Creates the socket instance
     *
     * @param   string socket default NULL
     * @return  peer.Socket
     */
    public function newInstance($socket= NULL) {
      if (NULL === $socket) $socket= $this->locate();

      // Workaround for PHP bug #29005: "fopen() can't open NT named pipes on local
      // computer". This seems to have been fixed in PHP 5.3 but we still support
      // PHP 5.2.10+ in 5.8-SERIES and 5.9-SERIES.
      if (0 === strncmp('\\\\.', $socket, 3)) {
        $socket= '\\\\127.0.0.1'.substr($socket, 3);
      }

      if (!($fd= fopen($socket, 'r+'))) {
        $e= new IOException('Cannot open pipe "'.$socket.'"');
        xp::gc(__FILE__);
        throw $e;
      }

      return new Socket(NULL, NULL, $fd);
    }
  }
?>
