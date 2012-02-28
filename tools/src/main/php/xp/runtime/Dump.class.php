<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Evaluates code and dumps its output.
   *
   */
  class xp·runtime·Dump extends Object {
    
    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $way= array_shift($args);

      // Read sourcecode from STDIN if no further argument is given
      if (0 === sizeof($args)) {
        $src= file_get_contents('php://stdin');
      } else {
        $src= $args[0];
      }
      $src= trim($src, ' ;').';';
      
      // Extract uses() and load classes
      if (0 === strncmp($src, 'uses', 4)) {
        $p= strpos($src, ');');
        $uses= substr($src, 5, $p- 5);    // "uses("
        $src= substr($src, $p+ 2);        // ");"
        foreach (explode(',', $uses) as $class) {
          uses(trim($class, '" '));
        }
      }
      
      // Allow missing return
      strstr($src, 'return ') || strstr($src, 'return;') || $src= 'return '.$src;

      // Rewrite argc, argv
      $argv= array(xp::nameOf(__CLASS__)) + $args;
      $argc= sizeof($argv);

      // Perform
      $return= eval($src);
      switch ($way) {
        case '-w': Console::writeLine($return); break;
        case '-d': var_dump($return); break;
      }
    }
  }
?>
