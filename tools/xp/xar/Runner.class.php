<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.xar';

  uses('util.cmd.Console', 'xp.xar.Options');

  /**
   * XAR command
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
     * Displays usage and exists
     *
     */
    protected static function usage() {
      Console::$err->writeLine('*** Usage: xar [options] [xarfile]');
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
      for ($i= 0; $i < sizeof($args); $i++) {
        if ('-R' == $args[$i]) {
          chdir($args[$i++]);
        } else if ('-?' == $args[$i]) {
          self::usage();
        } else {
          $options= 0;
          $offset= $i;
          for ($o= 0; $o < strlen($args[$i]); $o++) {
            switch ($args[$i]{$o}) {
              case 'c': 
                self::setOperation($operation, 'create'); 
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
              case 'v': 
                $options |= Options::VERBOSE; 
                break;
              case 'f': 
                $file= new File($args[$i+ 1]);
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
     
      // Perform operation
      $operation->newInstance(Console::$out, Console::$err, $options, new Archive($file), $args)->perform();
    } 
  }
?>
