<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'rdbms.ConnectionManager',
    'util.cmd.Command',
    'de.schlund.db.rubentest.Prozent_encode_test'
  );

  class PercentEncodeTest extends Command {

    /**
     * Main runner method
     *
     */
    public function run() {
      $testText= new Prozent_encode_test();
      $testText->setUserload(Prozent_encode_test::getById(1)->getUserload());
      $testText->save();
    }    

    /**
     * verbose output
     *
     */
    #[@arg]
    public function setVerbose() {
      Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
    }    
  }
?>
