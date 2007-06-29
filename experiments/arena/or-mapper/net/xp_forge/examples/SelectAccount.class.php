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
  class SelectAccount extends net·xp_forge·examples·AbstractExampleCommand {

    /**
     * Set criteria
     *
     * @param   string criteria default '*'
     */
    #[@arg(position= 0)]
    public function setCriteria($criteria= '*') {
      $this->criteria= new Criteria();
      if ('*' == $criteria) {
        // Select all
      } else if (is_numeric($criteria)) {
        $this->criteria->add(Account::column('account_id')->equal($criteria));
      } else if ('@' == $criteria{0}) {
        $this->criteria->add(Restrictions::equal(
          SQLFunctions::datediff('day', Account::column('lastchange'), Date::fromString(substr($criteria, 1))),
          0
        ));
      } else if (is_string($criteria)) {
        $this->criteria->add(Account::column('username')->like($criteria));
      }
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->out->writeLine('===> Selecting by ', $this->criteria);
      $rows= Account::getPeer()->doSelect($this->criteria);
      $this->out->writeLine($rows);
    }
  }
?>
