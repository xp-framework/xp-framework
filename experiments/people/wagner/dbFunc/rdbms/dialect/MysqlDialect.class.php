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
        'str_1'        => 'cast(%c as char)',
        'len_1'        => 'length(%c)',
        'getdate_0'    => 'sysdate()',
        'dateadd_3'    => 'timestampadd(%c, %c, %c)',
        'datediff_3'   => 'timestampdiff(%c, %c, %c)',
        'datename_2'   => 'cast(extract(%c from %c) as char)',
        'datepart_2'   => 'extract(%c from %c)',
      );
      
    /**
     * get an SQL function string
     *
     * @param   string func
     * @param   mixed[] function arguments string or rdbms.SQLFunction
     * @param   array types
     * @return  string
     */
    public function renderFunction($func, $args, $types) {
      $func_i= $func.'_'.count($args);
      for ($i= 0, $to= count($args); $i < $to; $i++) $args[$i]= is('rdbms.SQLFunction', $args[$i]) ? '('.$args[$i]->asSql($this->conn, $types).')' : $args[$i];

      if (isset(self::$implementation[$func_i])) return call_user_func_array(array($this->conn, 'prepare'), array_merge(array(self::$implementation[$func_i]), $args));
      return parent::renderFunction($func, $args, $types);
    }
  
  }
?>
