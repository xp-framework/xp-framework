<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.types.String', 'StringExtensions');

  /**
   * (Insert class' description here)
   *
   */
  class ExtensionMethodsIntegrationTestFixture extends Object {
    
    /**
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      try {
        Console::$out->writeLine('+', serialize(eval(Console::$in->readLine())));
      } catch (Throwable $e) {
        Console::$out->writeLine('-', $e->getMessage());
      }
    }
  }
?>
