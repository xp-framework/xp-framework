<?php namespace net\xp_framework\unittest\rdbms\mock;

/**
 * SQL dialect for MockConnections
 *
 * @see  xp://rdbms.SQLDialect
 * @see  xp://net.xp_framework.unittest.rdbms.mock.MockConnection
 */
class MockDialect extends \rdbms\SQLDialect {
  public
    $escape       = '"',
    $escapeRules  = array('"' => '""'),
    $escapeT      = '"',
    $escapeTRules = array('"' => '""'),
    $dateFormat   = 'Y-m-d h:iA';
      
  /**
   * get a function format string
   *
   * @param   rdbms.SQLFunction $func
   * @return  string
   * @throws  lang.IllegalArgumentException
   */
  public function formatFunction(\rdbms\SQLFunction $func) {
    if ('concat' == $func->func) {
      return '('.implode(' + ', array_fill(0, sizeof($func->args), '%s')).')';
    }
    return parent::formatFunction($func);
  }

  /**
   * Get a dialect specific datepart
   *
   * @param   string $datepart
   * @return  string
   * @throws  lang.IllegalArgumentException
   */
  public function datepart($datepart) {
    return false;
  }

  /**
   * Build join related part of an SQL query
   *
   * @param   rdbms.join.JoinRelation[] conditions
   * @return  string
   * @throws  lang.IllegalArgumentException
   */
  public function makeJoinBy(array $conditions) {
    if (0 == sizeof($conditions)) {
      throw new \lang\IllegalArgumentException('Conditions can not be empty');
    }

    $tableString= current($conditions)->getSource()->toSqlString();
    $conditionString= '';
    foreach ($conditions as $link) {
      $tableString.= ', '.$link->getTarget()->toSqlString();
      foreach ($link->getConditions() as $condition) $conditionString.= str_replace('=', '*=', $condition).' and ';
    }
    return $tableString.' where '.$conditionString;
  }
}
