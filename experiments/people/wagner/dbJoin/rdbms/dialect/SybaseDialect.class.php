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
      $implementation= array(
        'str_1'        => 'convert(varchar, %c)',
        'cast_2'       => 'convert(%c, %c)',
        'atan_2'       => 'atn2(%d, %d)',
        'ceil_1'       => 'ceiling(%d)',
        'degrees_1'    => 'convert(float, degrees(%c))',
        'radians_1'    => 'convert(float, radians(%c))',
        'sign_1'    => 'convert(int, sign(%c))',
      );
      
    /**
     * get an SQL function string
     *
     * @param   string func
     * @param   mixed[] function arguments string or rdbms.SQLFunction
     * @param   array types
     * @return  string
     */
    public function renderFunction($func, $args, $types, $aliasTable= '') {
      $tablePrefix= ($aliasTable) ? $aliasTable.'.' : '';
      $func_i= $func.'_'.count($args);
      for ($i= 0, $to= count($args); $i < $to; $i++) $args[$i]= is('rdbms.SQLFunction', $args[$i]) ? '('.$args[$i]->asSql($this->conn, $types, $aliasTable).')' : $tablePrefix.$args[$i];
      
      switch ($func) {
        case 'concat': return '('.implode(' + ', $args).')';

        default:
        if (isset(self::$implementation[$func_i])) return call_user_func_array(array($this->conn, 'prepare'), array_merge(array(self::$implementation[$func_i]), $args));
        return parent::renderFunction($func, $args, $types);
      }
    }
  
    /**
     * build join related part of an SQL query
     *
     * @param   string[]
     * @param   string[]
     * @return  string
     */
    public function makeJoinBy(Array $tables, Array $conditions) {
      if (1 >= count($tables)) return array_pop($tables);
      $querypart= implode(', ', $tables).' where ';
      foreach ($conditions as $relation) {
        foreach ($relation as $condition) {
          $querypart.= str_replace('=', '*=', $condition).' and ';
        }
      }
      return $querypart;
    }
  }
?>
