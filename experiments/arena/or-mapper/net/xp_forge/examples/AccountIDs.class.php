<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_forge.examples.AbstractExampleCommand');

  /**
   * Projections::max() / Projections::min() demo
   *
   * @purpose  Example
   */
  class AccountIDs extends net·xp_forge·examples·AbstractExampleCommand {

    /**
     * Runs this command
     *
     */
    public function run() {
      with ($id= Account::column('account_id')); {
        foreach (array(
          'smallest' => Projections::min($id, 'value'), 
          'largest'  => Projections::max($id, 'value'), 
        ) as $what => $projection) {
          $v= Account::getPeer()
            ->iteratorFor(create(new Criteria())->setProjection($projection))
            ->next()
            ->get('value')
          ;

          $this->out->writeLinef(
            '===> The %s ID value in the Account table is %d',
            $what,
            $v
          );
        }
      }
    }
  }
?>
