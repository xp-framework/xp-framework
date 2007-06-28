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
        $func= SQLFunctions::datediff(
          'DAY', 
          Account::column('lastchange'), 
          Date::fromString(substr($criteria, 1))
        );

        $this->criteria->add(newinstance('rdbms.criterion.Criterion', array($func, '%d', 0, EQUAL), '{
          public function __construct(SQLFunction $func, $type, $value, $op) {
            $this->func= $func;
            $this->value= $value;
            $this->op= $op;
            $this->type= $type;
          }

          public function asSql(DBConnection $conn, Peer $peer) {
            return $this->func->asSql($conn)." ".$conn->prepare(str_replace("?", $this->type, $this->op), $this->value);
          }
        }'));
      } else if (is_string($criteria)) {
        $this->criteria->add(Account::column('username')->like($criteria));
      }
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->out->writeLinef(
        '===> Selecting by <%s>',
        $this->criteria->toSql(
          ConnectionManager::getInstance()->getByHost('test-ds', 0), 
          Account::getPeer()
        )
      );
      
      $rows= Account::getPeer()->doSelect($this->criteria);
      $this->out->writeLine($rows);
    }
  }
?>
