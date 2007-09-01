<?php
/* This class is part of the XP framework
 *
 * $Id: MockDialect.class.php 10835 2007-07-19 09:08:50Z ruben $ 
 */

  namespace net::xp_framework::unittest::rdbms::mock;
  ::uses('rdbms.SQLDialect');

  /**
   * SQL dialect for MockConnections
   *
   * @see      xp://rdbms.SQLDialect
   * @see      xp://net.xp_framework.unittest.rdbms.mock.MockConnection
   * @purpose  unittest.rdbms.mock
   */
  class MockDialect extends rdbms::SQLDialect {
    public
      $escape       = '"',
      $escapeRules  = array('"' => '""'),
      $escapeT      = '"',
      $escapeTRules = array('"' => '""'),
      $dateFormat   = 'Y-m-d h:iA';
        
    /**
     * get a function format string
     *
     * @param   SQLFunction func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction( $func) {
      if ('concat' == $func->func) return '('.implode(' + ', array_fill(0, sizeof($func->args), '%s')).')';
      return parent::formatFunction($func);
    }
  
    /**
     * get a dialect specific datepart
     *
     * @param   string datepart
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function datepart($datepart) {
      return self::$dateparts[$datepart];
    }

    /**
     * build join related part of an SQL query
     *
     * @param   rdbms.join.JoinRelation[] conditions
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function makeJoinBy(Array $conditions) {
      if (0 == sizeof($conditions)) throw new lang::IllegalArgumentException('conditions can not be empty');
      $tableString.= current($conditions)->getSource()->toSqlString();
      $conditionString= '';

      foreach ($conditions as $link) {
        $tableString.= ', '.$link->getTarget()->toSqlString();
        foreach ($link->getConditions() as $condition) $conditionString.= str_replace('=', '*=', $condition).' and ';
      }
      return $tableString.' where '.$conditionString;
    }
  }
?>
