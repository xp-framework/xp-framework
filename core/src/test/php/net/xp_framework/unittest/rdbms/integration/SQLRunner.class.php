<?php namespace net\xp_framework\unittest\rdbms\integration;

use rdbms\DriverManager;
use util\cmd\Console;

/**
 * SQL Runner used inside deadlock tests
 *
 * @see   xp://net.xp_framework.unittest.rdbms.integration.AbstractDeadlockTest
 */
class SQLRunner extends \lang\Object {
  
  /**
   * Entry point
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $db= DriverManager::getConnection($args[0]);
    try {
      $db->connect();
      $tran= $db->begin(new \rdbms\Transaction('process'));

      Console::$out->writeLine('! Started');
      while ($sql= Console::$in->readLine()) {
        $db->query($sql);
        Console::$out->writeLine('+ OK');
      }
      
      $tran->commit();
    } catch (\rdbms\SQLException $e) {
      Console::$out->writeLine('- ', $e->getClassName());
    }
  }
}
