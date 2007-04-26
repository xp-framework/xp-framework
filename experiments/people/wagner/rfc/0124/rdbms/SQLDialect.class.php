<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'rdbms.dialect.MysqlDialect',
    'rdbms.dialect.SybaseDialect'
  );

  /**
   * helps to build functions for different SQL servers
   *
   */
  abstract class SQLDialect extends Object {
    private static
      $instances= array(),
      $implementation= array(
        'second_1'     => 'second(%c)',
        'minute_1'     => 'minute(%c)',
        'hour_1'       => 'hour(%c)',
        'day_1'        => 'day(%c)',
        'month_1'      => 'month(%c)',
        'year_1'       => 'year(%c)',
        'nullif_2'     => 'nullif(%c, %c)',
        'substring_3'  => 'substring(%c, %c, %c)',
        'locate_2'     => 'locate(%c, %c)',
        'locate_3'     => 'locate(%c, %c, %c)',
        'trim_2'       => 'trim(%c)',
        'trim_3'       => 'trim(%c, %c)',
        'ltrim_2'      => 'trim(%c)',
        'ltrim_3'      => 'trim(%c, %c)',
        'rtrim_2'      => 'trim(%c)',
        'rtrim_3'      => 'trim(%c, %c)',
        'length_1'     => 'length(%c)',
        'bit_length_1' => 'bit_length(%c)',
        'upper_1'      => 'upper(%c)',
        'lower_1'      => 'lower(%c)',
        'cast_2'       => 'cast(%c as %c)',
        'str_1'        => 'str(%c)',
        'ascii_1'      => 'ascii(%c)',
        'char_1'       => 'char(%c)',
        'len_1'        => 'len(%c)',
        'reverse_1'    => 'reverse(%c)',
        'space_1'      => 'space(%c)',
        'soundex_1'    => 'soundex(%c)',
        'getdate_0'    => 'getdate()',
        'dateadd_3'    => 'dateadd(%c, %c, %c)',
        'datename_2'   => 'datename(%c, %c)',
        'datepart_2'   => 'datepart(%c, %c)',
        'abs_1'        => 'abs(%c)',
        'acos_1'       => 'acos(%c)',
        'asin_1'       => 'asin(%c)',
        'atan_1'       => 'atan(%c)',
        'atan_2'       => 'atan2(%c, %c)',
        'ceil_1'       => 'ceil(%c)',
        'cos_1'        => 'cos(%c)',
        'cot_1'        => 'cot(%c)',
        'degees_1'     => 'degees(%c)',
        'exp_1'        => 'exp(%c)',
        'floor_1'      => 'floor(%c)',
        'log_1'        => 'log(%c)',
        'log10_1'      => 'log10(%c)',
        'pi_0'         => 'pi()',
        'power_2'      => 'power(%c, %c)',
        'floor_1'      => 'floor(%c)',
        'radians_1'    => 'radians(%c)',
        'rand_0'       => 'rand()',
        'round_2'      => 'round(%c, %c)',
        'sign_1'       => 'sign(%c)',
        'sin_1'        => 'sin(%c)',
        'sqrt_1'       => 'sqrt(%c)',
        'tan_1'        => 'tan(%c)',
      );

    protected
      $conn= NULL;

    /**
     * Constructor
     *
     * @param   rdbms.DBConnection conn
     */
    public function __construct($conn) {
      $this->conn= $conn;
    }

    /**
     * Get an SQL dialect for a connection  
     *
     * @param   rdbms.DBConnection conn
     * @return  rdbms.SQLDialect
     */
    public static function getDialect($conn) {
      $scheme= $conn->dsn->url->getScheme();
      if (!isset($instances[$scheme])) {
        switch($scheme) {
          case 'mysql':  $instances[$scheme]= new MysqlDialect($conn); break;
          case 'sybase': $instances[$scheme]= new SybaseDialect($conn); break;
        }
      }
      return $instances[$scheme];
    }
    
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
      return $func.'('.implode(', ', $args).')';
    }

  }
?>
