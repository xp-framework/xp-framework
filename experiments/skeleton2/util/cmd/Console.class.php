<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.ConsoleOutputStream');

  /**
   * Represents system console
   *
   * <code>
   *   uses('util.cmd.Console');
   *
   *   Console::writeLine('Hello ', 'a', 'b', 1);   // Hello ab1
   *   Console::writeLinef('Hello %s', 'World');    // Hello World
   * </code>
   *
   * @see      http://msdn.microsoft.com/library/default.asp?url=/library/en-us/cpref/html/frlrfSystemConsoleClassTopic.asp
   * @model    static
   * @purpose  I/O functions
   */
  class Console extends Object {
    public static 
      $out = NULL,
      $err = NULL;
      
    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() {
      Console::$out= new ConsoleOutputStream(STDOUT);
      Console::$err= new ConsoleOutputStream(STDOUT);
    }
    
    /**
     * Flush output buffer
     *
     * @model   static
     * @access  public
     */
    public static function flush() {
      fflush(STDOUT);
    }

    /**
     * Write a string to standard output
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    public static function write() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a));
    }
    
    /**
     * Write a string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   mixed* args
     */
    public static function writeLine() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a)."\n");
    }
    
    /**
     * Write a formatted string to standard output
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     * @see     php://printf
     */
    public static function writef() {
      $a= func_get_args();
      fwrite(STDOUT, vsprintf(array_shift($a), $a));
    }

    /**
     * Write a formatted string to standard output and append a newline
     *
     * @model   static
     * @access  public
     * @param   string format
     * @param   mixed* args
     */
    public static function writeLinef() {
      $a= func_get_args();
      fwrite(STDOUT, vsprintf(array_shift($a), $a)."\n");
    }
    
    /**
     * Read a line from standard input. The line ending (\r and/or \n)
     * is trimmed off the end.
     *
     * @model   static
     * @access  public
     * @return  string
     */    
    public static function readLine() {
      $r= '';
      while ($bytes= fgets(STDIN, 0x20)) {
        $r.= $bytes;
        if (FALSE !== strpos("\r\n", substr($r, -1))) break;
      }
      return rtrim($r, "\r\n");
    }

    /**
     * Read a single character from standard input.
     *
     * @model   static
     * @access  public
     * @return  string
     */    
    public static function read() {
      return fgetc(STDIN);
    }
  }
?>
