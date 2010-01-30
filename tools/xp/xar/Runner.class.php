<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.xar';

  uses('util.cmd.Console', 'xp.xar.Options', 'io.File');

  /**
   * XAR
   * ===
   * This tool can be used for working with XAR archives.
   *
   * Usage:
   * <pre>
   *   $ xar {options} {xarfile} [{fileset}]
   * </pre>
   *
   * Creating a xar file
   * -------------------
   * The following creates a xar file containing all files inside the
   * directories "src" and "lib" as well as the file "etc/config.ini".
   *
   * <tt>$ xar cf app.xar src/ lib/ etc/config.ini</tt>
   *
   * Extracting a xar file
   * ---------------------
   * The following extracts all files inside the "app.xar" into the 
   * current directory. Directories and files are created if necessary,
   * existing files are overwritten.
   * 
   * <tt>$ xar xf app.xar</tt>
   *
   * Viewing an archive's contents
   * -----------------------------
   * To list what's inside a xar file, use the following command:
   *
   * <tt>$ xar tf app.xar</tt>
   *
   * @purpose  Tool
   */
  class xp·xar·Runner extends Object {

    /**
     * Set operation
     *
     * @param   xp.xar.instruction.AbstractInstruction operation
     * @param   string name
     */
    protected static function setOperation(&$operation, $name) {
      if (NULL !== $operation) {
        self::bail('Cannot execute more than one instruction at a time.');
      }
      $operation= Package::forName('xp.xar.instruction')->loadClass(ucfirst($name).'Instruction');
    }
    
    /**
     * Converts api-doc "markup" to plain text w/ ASCII "art"
     *
     * @param   string markup
     * @return  string text
     */
    protected static function textOf($markup) {
      $line= str_repeat('=', 72);
      return strip_tags(preg_replace(array(
        '#<pre>#', '#</pre>#', '#<li>#',
      ), array(
        $line, $line, '* ',
      ), trim($markup)));
    }

    /**
     * Displays usage and exists
     *
     */
    protected static function usage() {
      Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      exit(1);
    }

    /**
     * Displays a message and exists
     *
     */
    protected static function bail($message) {
      Console::$err->writeLine('*** ', $message);
      exit(1);
    }
  
    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (!$args) self::usage();
      
      // Parse command line
      $operation= NULL;
      $std= 'php://stdin';
      for ($i= 0; $i < sizeof($args); $i++) {
        if ('-R' == $args[$i]) {
          chdir($args[++$i]);
        } else if (in_array($args[$i], array('-?', '-h', '--help'))) {
          self::usage();
        } else {
          $options= 0;
          $offset= $i;
          for ($o= 0; $o < strlen($args[$i]); $o++) {
            switch ($args[$i]{$o}) {
              case 'c': 
                self::setOperation($operation, 'create'); 
                $std= 'php://stdout';
                break;
              case 'x': 
                self::setOperation($operation, 'extract'); 
                break;
              case 's': 
                self::setOperation($operation, 'show'); 
                break;
              case 'd': 
                self::setOperation($operation, 'diff'); 
                break;
              case 't':
                self::setOperation($operation, 'extract');
                $options |= Options::SIMULATE | Options::VERBOSE;
                break;
              case 'm':
                self::setOperation($operation, 'merge');
                $std= 'php://stdout';
                break;
              case 'v': 
                $options |= Options::VERBOSE; 
                break;
              case 'f': 
                $file= new File($args[$i+ 1]);
                $std= NULL;
                $offset++;
                break;
              default: 
                self::bail('Unsupported commandline option "'.$args[$i].'"');
            }
          }
          $args= array_slice($args, $offset+ 1);
          break;
        }
      }
      
      if (!$operation) self::usage();
      
      // Use STDOUT & STDERR if no file is given
      if ($std) $file= new File($std);
     
      // Perform operation
      $operation->newInstance(Console::$out, Console::$err, $options, new Archive($file), $args)->perform();
    } 
  }
?>
