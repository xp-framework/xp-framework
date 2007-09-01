<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::pgsql;
  uses('rdbms.SQLDialect');

  /**
   * helps to build functions for different SQL servers
   *
   */
  class PostgreSQLDialect extends rdbms::SQLDialect {
    private static
      $dateparts= array(
        'dayofweek' => 'dow',
        'dayofyear' => 'doy',
      ),
      $implementations= array(
        'rand_0'       => 'random()',
        'trim_1'       => "trim(both ' ' from %s)",
        'trim_2'       => 'trim(both %2s from %1s)',
        'rtrim_1'      => 'rtrim(%s)',
        'rtrim_2'      => 'rtrim(%s, %s)',
        'ltrim_1'      => 'ltrim(%s)',
        'ltrim_2'      => 'ltrim(%s, %s)',
        'substring_3'  => 'substring(%s from %s for %s)',
        'substring_2'  => 'substring(%s from %s)',
      );

    public
      $escape       = "'",
      $escapeRules  = array("'"  => "''"),
      $escapeT      = '"',
      $escapeTRules = array('"'  => '""'),
      $dateFormat   = 'Y-m-d H:i:s';
        
    /**
     * get a function format string
     *
     * @param   SQLFunction func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction( $func) {
      $func_i= $func->func.'_'.sizeof($func->args);
      switch ($func->func) {
        case 'concat':
        return '('.implode(' || ', array_fill(0, sizeof($func->args), '%s')).')';

        default:
        if (isset(self::$implementations[$func_i])) return self::$implementations[$func_i];
        return parent::formatFunction($func);
      }
    }
  
    /**
     * get a dialect specific datepart
     *
     * @param   string datepart
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function datepart($datepart) {
      $datepart= strToLower($datepart);
      if (!array_key_exists($datepart, self::$dateparts)) return parent::datepart($datepart);
      if (FALSE === self::$dateparts[$datepart]) throw new lang::IllegalArgumentException('PostgreSQL does not support datepart '.$datepart);
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
      $querypart= '';
      $first= TRUE;
      foreach ($conditions as $link) {
        if ($first) {
          $first= FALSE;
          $querypart.= sprintf(
            '%s LEFT OUTER JOIN %s on (%s) ',
            $link->getSource()->toSqlString(),
            $link->getTarget()->toSqlString(),
            implode(' and ', $link->getConditions())
          );
        } else {
          $querypart.= sprintf('LEFT JOIN %s on (%s) ', $link->getTarget()->toSqlString(), implode(' and ', $link->getConditions()));
        }
      }
      return $querypart.'where ';
    }
  }
?>
