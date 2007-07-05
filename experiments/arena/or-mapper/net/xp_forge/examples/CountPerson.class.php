<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_forge.examples.AbstractExampleCommand',
    'rdbms.SQLFunctions'
  );

  /**
   * Selects an account
   *
   * @purpose  Example
   */
  class CountPerson extends net·xp_forge·examples·AbstractExampleCommand {

    /**
     * Runs this command
     *
     */
    public function run() {
      $this->out->writeLine(Person::getPeer()
        ->iteratorFor(create(new Criteria())->setProjection(Projections::count()))
        ->next()
      );
    }
  }
?>
