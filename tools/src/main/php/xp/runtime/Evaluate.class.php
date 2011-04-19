<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Displays XP version and runtime information
   *
   * @purpose  Tool
   */
  class xp·runtime·Evaluate extends Object {
    
    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $src= $args[0];
      $argv= array(xp::nameOf(__CLASS__)) + $args;
      $argc= sizeof($argv);
      exit(eval($src));
    }
  }
?>
