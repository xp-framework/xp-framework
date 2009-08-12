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
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SQLRunner extends Object {
    
    public static function main(array $args) {
      $db= DriverManager::getConnection($args[0]);
      try {
        $db->connect();
        $tran= $db->begin(new Transaction('process'));

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
