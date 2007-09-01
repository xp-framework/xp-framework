<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::sybase;
  uses('rdbms.SQLDialect');

  /**
   * helps to build functions for different SQL servers
   *
   */
  class SybaseDialect extends rdbms::SQLDialect {
    private static
      $dateparts= array(
        'microsecond' => FALSE,
      ),
      $implementations= array(
        'str_1'      => 'convert(varchar, %s)',
        'cast_2'     => 'convert(%s, %c)',
        'atan_2'     => 'atn2(%d, %d)',
        'ceil_1'     => 'ceiling(%d)',
        'degrees_1'  => 'convert(float, degrees(%d))',
        'radians_1'  => 'convert(float, radians(%d))',
        'sign_1'     => 'convert(int, sign(%d))',
      );
      
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
    public function formatFunction(rdbms::SQLFunction $func) {
      $func_i= $func->func.'_'.sizeof($func->args);
      switch ($func->func) {
        case 'concat':
        return '('.implode(' + ', array_fill(0, sizeof($func->args), '%s')).')';

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
      if (FALSE === self::$dateparts[$datepart]) throw new lang::IllegalArgumentException('SYBASE does not support datepart '.$datepart);
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
      $tableString.= sprintf('%s', current($conditions)->getSource()->toSqlString());
      $conditionString= '';

      foreach ($conditions as $link) {
        $tableString.= sprintf(', %s', $link->getTarget()->toSqlString());
        foreach ($link->getConditions() as $condition) $conditionString.= str_replace('=', '*=', $condition).' and ';
      }
      return $tableString.' where '.$conditionString;
    }
  }
?>
