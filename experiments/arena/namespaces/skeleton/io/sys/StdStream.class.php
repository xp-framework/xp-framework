<?php
/* This class is part of the XP framework
 *
 * $Id: StdStream.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace io::sys;

  ::uses('io.File');

  /**
   * Standard I/O streams
   *
   * @see      http://www.opengroup.org/onlinepubs/007908799/xsh/stdin.html
   * @purpose  Wrap standard I/O streams with File objects
   */
  class StdStream extends lang::Object {
  
    /**
     * Retrieve a file object
     *
     * <code>
     *   $stdout= &StdStream::get(STDOUT);
     *   $stdout->write('Hello');
     * </code>
     *
     * @param   resource handle one of STDIN | STDOUT | STDERR
     * @return  io.File
     */
    public static function get($handle) {
      static $f= array();
      
      if (!isset($f[$handle])) {
        $f[$handle]= new io::File($handle);
      }
      return $f[$handle];
    }
  
  }
?>
