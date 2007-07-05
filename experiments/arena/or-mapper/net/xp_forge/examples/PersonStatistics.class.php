<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_forge.examples.AbstractExampleCommand');

  /**
   * Projections demo
   *
   * @purpose  Example
   */
  class PersonStatistics extends net·xp_forge·examples·AbstractExampleCommand {
    protected
      $criteria= NULL;

    /**
     * Set field to aggregate by
     *
     * @param   string field default 'bz_id'
     */
    #[@arg(position= 0)]
    public function setField($field= 'bz_id') {
      $this->criteria= create(new Criteria())
        ->setProjection(Projections::projectionList(array(
          Projections::property(Person::column($field)), 
          Projections::count()
        )))
        ->addGroupBy($field)
      ;
    }

    /**
     * Runs this command
     *
     */
    public function run() {
      for ($i= Person::getPeer()->iteratorFor($this->criteria); $i->hasNext(); ) {
        $this->out->writeLine('- ', $i->next());
      }
    }
  }
?>
