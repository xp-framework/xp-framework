<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbm.SQLDialect');

  /**
   * helps to build functions for different SQL servers
   *
   */
  class MysqlDialect extends SQLDialect {
    private static
      $dateparts= array(
        'DAYOFYEAR'	  => FALSE,
        'WEEKDAY'	  => FALSE,
        'MILLISECOND' => FALSE,
      ),
      $implementations= array(
        'str_1'        => 'cast(%s as char)',
        'len_1'        => 'length(%s)',
        'getdate_0'    => 'sysdate()',
        'dateadd_3'    => 'timestampadd(%t, %d, %s)',
        'datediff_3'   => 'timestampdiff(%t, %s, %s)',
        'datename_2'   => 'cast(extract(%t from %s) as char)',
        'datepart_2'   => 'extract(%t from %s)',
      );

    public
      $escape       = '"',
      $escapeRules  = array(
        '"'   => '\"',
        '\\'  => '\\\\'
      ),
      $dateFormat   = 'Y-m-d H:i:s';
        
    /**
     * get a function format string
     *
     * @param   SQLFunction $func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction(SQLFunction $func) {
      $func_i= $func->func.'_'.count($func->args);
      switch ($func->func) {
        case 'concat':
        return 'concat('.implode(', ', array_fill(0, count($func->args), '%s')).')';

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
      $datepart= strToUpper($datepart);
      if (!array_key_exists($datepart, self::$dateparts)) return parent::datepart($datepart);
      if (FALSE === self::$dateparts[$datepart]) throw new IllegalArgumentException('MYSQL does not support datepart '.$datepart);
      return self::$dateparts[$datepart];
    }

  }
?>
