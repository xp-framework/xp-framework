<?php namespace xp\scriptlet;

/**
 * Scriptlet usage
 */
class Usage extends \lang\Object {

  /**
   * Entry point method
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    \util\cmd\Console::$err->writeLine(\lang\XPClass::forName(\xp::nameOf(__CLASS__))->getPackage()->getResource($args[0]));
    return 0xFF;
  }
}
