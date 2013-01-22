<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Evaluates sourcecode
   *
   */
  class xp·runtime·Evaluate extends Object {
    
    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {

      // Read sourcecode from STDIN if no further argument is given
      if (0 === sizeof($args)) {
        $src= file_get_contents('php://stdin');
      } else {
        $src= $args[0];
      }
      $src= trim($src, ' ;').';';

      // Perform
      $argv= array(xp::nameOf(__CLASS__)) + $args;
      $argc= sizeof($argv);
      return eval($src);
    }
  }
?>
