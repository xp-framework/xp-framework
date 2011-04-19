<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Standard I/O streams
   *
   * @see      http://www.opengroup.org/onlinepubs/007908799/xsh/stdin.html
   * @purpose  Wrap standard I/O streams with File objects
   */
  class StdStream extends Object {
  
    /**
     * Retrieve a file object
     *
     * <code>
     *   $stdout= StdStream::get(STDOUT);
     *   $stdout->write('Hello');
     * </code>
     *
     * @param   resource handle one of STDIN | STDOUT | STDERR
     * @return  io.File
     */
    public static function get($handle) {
      static $f= array();
      
      if (!isset($f[$handle])) {
        $f[$handle]= new File($handle);
      }
      return $f[$handle];
    }
  
  }
?>
