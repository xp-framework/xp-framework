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
      $implementation= array(
        'str_1'        => 'cast(%s as char)',
        'len_1'        => 'length(%s)',
        'getdate_0'    => 'sysdate()',
        'dateadd_3'    => 'timestampadd(%c, %d, %s)',
        'datediff_3'   => 'timestampdiff(%c, %s, %s)',
        'datename_2'   => 'cast(extract(%c from %s) as char)',
        'datepart_2'   => 'extract(%c from %s)',
      );
      
    /**
     * get an SQL function string
     *
     * @param   string func
     * @param   mixed[] function arguments string or rdbms.SQLFunction
     * @return  string
     */
    public function renderFunction($func, $args) {
      $func_i= $func.'_'.count($args);
      switch ($func) {
        case 'concat':
        $fmt= 'concat('.implode(', ', array_fill(0, count($args), '%s')).')';
        array_unshift($args, $fmt);
        return call_user_func_array(array($this->conn, 'prepare'), $args);

        default:
        if (isset(self::$implementation[$func_i])) {
          array_unshift($args, self::$implementation[$func_i]);
          return call_user_func_array(array($this->conn, 'prepare'), $args);
        }
        return parent::renderFunction($func, $args);
      }
    }
  
  }
?>
