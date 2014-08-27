<?php namespace xp\runtime;

use util\cmd\Console;

/**
 * Evaluates sourcecode
 *
 */
class Evaluate extends \lang\Object {
  
  /**
   * Main
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $argc= sizeof($args);

    // Read sourcecode from STDIN if no further argument is given
    if (0 === $argc) {
      $src= file_get_contents('php://stdin');
    } else if ('--' === $args[0]) {
      $src= file_get_contents('php://stdin');
    } else {
      $src= $args[0];
    }

    // Support <?php
    $src= trim($src, ' ;').';';
    if (0 === strncmp($src, '<?php', 5)) {
      $src= substr($src, 6);
    }

    // Perform
    $argv= array(\xp::nameOf(__CLASS__)) + $args;
    $argc= sizeof($argv);
    return eval($src);
  }
}
