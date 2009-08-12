<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.DriverManager',
    'util.cmd.Console'
  );

  /**
   * SQL Runner used inside deadlock tests
   *
   * @see   xp://net.xp_framework.unittest.rdbms.integration.AbstractDeadlockTest
   */
  class SQLRunner extends Object {
    
    /**
     * Entry point
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $db= DriverManager::getConnection($args[0]);
      try {
        $db->connect();
        $tran= $db->begin(new Transaction('process'));

        Console::$out->writeLine('! Started');
        while ($sql= Console::$in->readLine()) {
          $db->query($sql);
          Console::$out->writeLine('+ OK');
        }
        
        $tran->commit();
      } catch (SQLException $e) {
        Console::$out->writeLine('- ', $e->getClassName());
      }
    }
  }
?>
