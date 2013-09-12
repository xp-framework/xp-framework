<?php namespace xp\xar;

use util\cmd\Console;
use io\File;

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
 * Option synopsis
 * ===============
 *  -c        Create archive
 *  -x        Extract archive
 *  -t        List archive contents
 *  -s        See file`s contents
 *  -d        Diff archives
 *  -m        Merge archives
 *
 *
 * Command details
 * ===============
 *
 * Creating a xar file
 * -------------------
 * The following creates a xar file containing all files inside the
 * directories "src" and "lib" as well as the file "etc/config.ini".
 *
 * <tt>$ xar cf app.xar src/ lib/ etc/config.ini</tt>
 *
 *
 * Extracting a xar file
 * ---------------------
 * The following extracts all files inside the "app.xar" into the 
 * current directory. Directories and files are created if necessary,
 * existing files are overwritten.
 * 
 * <tt>$ xar xf app.xar</tt>
 *
 *
 * Viewing an archive's contents
 * -----------------------------
 * To list what's inside a xar file, use the following command:
 *
 * <tt>$ xar tf app.xar</tt>
 *
 *
 * Viewing the contents of a contained file
 * ----------------------------------------
 * To view a single file from a given archive, use the following command:
 *
 * <tt>$ xar sf archive.xar path/to/file/in/archive</tt>
 *
 *
 * Merging multiple archives
 * -------------------------
 * To merge archives into a single new one, use the following command:
 *
 * <tt>$ xar mf new.xar old-archive-1.xar old-archive-2.xar</tt>
 *
 *
 * Comparing two archives
 * ----------------------
 * To compare two archives, use the following command:
 *
 * <tt>$ xar df one.xar two.xar</tt>
 */
class Runner extends \lang\Object {

  /**
   * Set operation
   *
   * @param   xp.xar.instruction.AbstractInstruction operation
   * @param   string name
   */
  protected static function setOperation(&$operation, $name) {
    if (null !== $operation) {
      self::bail('Cannot execute more than one instruction at a time.');
    }
    $operation= \lang\reflect\Package::forName('xp.xar.instruction')->loadClass(ucfirst($name).'Instruction');
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
   * @return  int
   */
  protected static function usage() {
    Console::$err->writeLine(self::textOf(\lang\XPClass::forName(\xp::nameOf(__CLASS__))->getComment()));
    return 1;
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
    if (!$args) return self::usage();
    
    // Parse command line
    $operation= null;
    $std= 'php://stdin';
    for ($i= 0; $i < sizeof($args); $i++) {
      if ('-R' == $args[$i]) {
        chdir($args[++$i]);
      } else if (in_array($args[$i], array('-?', '-h', '--help'))) {
        return self::usage();
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
              $std= null;
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
    
    if (!$operation) return self::usage();
    
    // Use STDOUT & STDERR if no file is given
    if ($std) $file= new File($std);
   
    // Perform operation
    $operation->newInstance(Console::$out, Console::$err, $options, new \lang\archive\Archive($file), $args)->perform();
  } 
}
