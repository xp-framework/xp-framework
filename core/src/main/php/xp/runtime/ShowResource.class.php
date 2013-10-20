<?php namespace xp\runtime;

use util\cmd\Console;

/**
 * Shows a given resource from the package this class is contained in
 * on standard error and exit with a given value.
 *
 * Example:
 * <pre>
 *   $ xp ShowResource usage.txt 255
 * </pre>
 */
class ShowResource extends \lang\Object {

  /**
   * Main
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    Console::$err->writeLine(\lang\XPClass::forName(\xp::nameOf(__CLASS__))->getPackage()->getResource($args[0]));
    return (int)$args[1];
  }
}
