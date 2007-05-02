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
  class SybaseDialect extends SQLDialect {
    private static
      $dateparts= array(
        'MICROSECOND' => FALSE,
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
      $dateFormat   = 'Y-m-d h:iA';
        
    /**
     * get a function format string
     *
     * @param   SQLFunction $func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction(SQLFunction $func) {
      $func_i= $func->func.'_'.count($func->args);
      switch ($func) {
        case 'concat':
        return '('.implode(' + ', array_fill(0, count($func->args), '%s')).')';

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
      if (FALSE === self::$dateparts[$datepart]) throw new IllegalArgumentException('SYBASE does not support datepart '.$datepart);
      return self::$dateparts[$datepart];
    }

  }
?>
