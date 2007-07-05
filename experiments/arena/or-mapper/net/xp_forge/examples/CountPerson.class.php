<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_forge.examples.AbstractExampleCommand');

  /**
   * Projections::count() demo
   *
   * @purpose  Example
   */
  class CountPerson extends net·xp_forge·examples·AbstractExampleCommand {

    /**
     * Runs this command
     *
     */
    public function run() {
      $c= Person::getPeer()
        ->iteratorFor(create(new Criteria())->setProjection(Projections::count()))
        ->next()
        ->get('count')
      ;

      $this->out->writeLinef(
        '===> The person table contains %s',
        0 == $c ? 'no entries' : (1 == $c ? 'one entry' : $c.' entries')
      );
    }
  }
?>
