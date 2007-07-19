<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'de.schlund.db.rubentest.Nmappoint',
    'de.schlund.db.rubentest.Mperson',
    'util.cmd.Command'
  );
 
  class ConstraintTest extends Command {

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine(Nmappoint::getByCoord_xCoord_y(1, 2)->getTexture());
      $this->out->writeLine(Mperson::getPeer()->doSelect(new Criteria(Restrictions::equal('person_id', 3))));
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
