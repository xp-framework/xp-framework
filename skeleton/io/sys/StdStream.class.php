<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  // Define default filehandles
  if (!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'r'));          
  if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));          
  if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));         
  
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
     *   $stdout= &StdStream::get(STDOUT);
     *   $stdout->write('Hello');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   resource handle one of STDIN | STDOUT | STDERR
     * @return  &io.File
     */
    function &get($handle) {
      static $f= array();
      
      if (!isset($f[$handle])) {
        $f[$handle]= &new File(NULL);
        $f[$handle]->_fd= $handle;
      }
      return $f[$handle];
    }
  
  }
?>
