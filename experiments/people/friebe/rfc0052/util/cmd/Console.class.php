/* This class is part of the XP framework's experiments
 *
 * $Id$ <?php
 */

package util·cmd {
  public class Console {

    /**
     * Flush output buffer
     *
     */
    public static function flush() {
      fflush(STDOUT);
    }

    /**
     * Write a string to standard output
     *
     * @param   mixed* args
     */
    public static function write() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a));
    }
    
    /**
     * Write a string to standard output and append a newline
     *
     * @param   mixed* args
     */
    public static function writeLine() {
      $a= func_get_args();
      fwrite(STDOUT, implode('', $a)."\n");
    }
    
    /**
     * Write a formatted string to standard output
     *
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
     * @param   string prompt = NULL
     * @return  string
     */    
    public static function readLine($prompt= NULL) {
      $prompt && Console::write($prompt.' ');
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
     * @param   string prompt = NULL
     * @return  string
     */    
    public static function read($prompt= NULL) {
      $prompt && Console::write($prompt.' ');
      return fgetc(STDIN);
    }
  }
}
