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
        'str_1'      => 'convert(varchar, %s)',
        'cast_2'     => 'convert(%s, %c)',
        'atan_2'     => 'atn2(%d, %d)',
        'ceil_1'     => 'ceiling(%d)',
        'datediff_3' => 'timestampdiff(%c, %s, %s)',
        'degrees_1'  => 'convert(float, degrees(%d))',
        'radians_1'  => 'convert(float, radians(%d))',
        'sign_1'     => 'convert(int, sign(%d))',
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
        $fmt= '('.implode(' + ', array_fill(0, count($args), '%s')).')';
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
