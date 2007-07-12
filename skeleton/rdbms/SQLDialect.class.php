<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * helps to build statments for different SQL servers
   *
   * @purpose  Base class for all dialects 
   * @test     net.xp_framework.unittest.rdbms.SQLDialectTest
   */
  abstract class SQLDialect extends Object {
    private static
      $dateparts= array(
        'day'         => 'day',
        'dayofyear'   => 'dayofyear',
        'hour'        => 'hour',
        'microsecond' => 'microsecond',
        'millisecond' => 'millisecond',
        'minute'      => 'minute',
        'month'       => 'month',
        'quarter'     => 'quarter',
        'second'      => 'second',
        'week'        => 'week',
        'weekday'     => 'weekday',
        'year'        => 'year',
      ),
      $datatypes= array(
        'bigint'     => 'bigint',
        'binary'     => 'binary',
        'blob'       => 'blob',
        'char'       => 'char',
        'clob'       => 'clob',
        'date'       => 'date',
        'datetime'   => 'datetime',
        'dec'        => 'dec',
        'decimal'    => 'decimal',
        'double'     => 'double',
        'float'      => 'float',
        'int'        => 'int',
        'integer'    => 'integer',
        'smallint'   => 'smallint',
        'text'       => 'text',
        'time'       => 'time',
        'timestamp'  => 'timestamp',
        'varbinary'  => 'varbinary',
        'varchar'    => 'varchar',
      ),
      $implementations= array(
        'abs_1'        => 'abs(%d)',
        'acos_1'       => 'acos(%d)',
        'ascii_1'      => 'ascii(%s)',
        'asin_1'       => 'asin(%d)',
        'atan_1'       => 'atan(%d)',
        'atan_2'       => 'atan2(%d, %d)',
        'bit_length_1' => 'bit_length(%s)',
        'cast_2'       => 'cast(%s as %e)',
        'ceil_1'       => 'ceil(%d)',
        'char_1'       => 'char(%d)',
        'cos_1'        => 'cos(%d)',
        'cot_1'        => 'cot(%d)',
        'dateadd_3'    => 'dateadd(%t, %d, %s)',
        'datediff_3'   => 'datediff(%t, %s, %s)',
        'datename_2'   => 'datename(%t, %s)',
        'datepart_2'   => 'datepart(%t, %s)',
        'day_1'        => 'day(%s)',
        'degrees_1'    => 'degrees(%d)',
        'exp_1'        => 'exp(%d)',
        'floor_1'      => 'floor(%d)',
        'getdate_0'    => 'getdate()',
        'hour_1'       => 'hour(%s)',
        'len_1'        => 'len(%s)',
        'length_1'     => 'length(%s)',
        'locate_2'     => 'locate(%s, %s)',
        'locate_3'     => 'locate(%s, %s, %s)',
        'log10_1'      => 'log10(%d)',
        'log_1'        => 'log(%d)',
        'lower_1'      => 'lower(%s)',
        'ltrim_1'      => 'trim(%s)',
        'ltrim_2'      => 'trim(%s, %s)',
        'minute_1'     => 'minute(%s)',
        'month_1'      => 'month(%s)',
        'nullif_2'     => 'nullif(%s, %s)',
        'pi_0'         => 'pi()',
        'power_2'      => 'power(%d, %d)',
        'radians_1'    => 'radians(%d)',
        'rand_0'       => 'rand()',
        'reverse_1'    => 'reverse(%s)',
        'round_2'      => 'round(%d, %d)',
        'rtrim_1'      => 'trim(%s)',
        'rtrim_2'      => 'trim(%s, %s)',
        'second_1'     => 'second(%s)',
        'sign_1'       => 'sign(%d)',
        'sin_1'        => 'sin(%d)',
        'soundex_1'    => 'soundex(%s)',
        'space_1'      => 'space(%d)',
        'sqrt_1'       => 'sqrt(%d)',
        'str_1'        => 'str(%s)',
        'substring_3'  => 'substring(%s, %s, %s)',
        'substring_2'  => 'substring(%s, %s)',
        'tan_1'        => 'tan(%d)',
        'trim_2'       => 'trim(%s)',
        'trim_3'       => 'trim(%s, %s)',
        'upper_1'      => 'upper(%s)',
        'year_1'       => 'year(%s)',
        'add_2'        => '%f + %f',
        'sub_2'        => '%f - %f',
        'mul_2'        => '%f * %f',
        'div_2'        => '%f % %f',
        'bitAnd_2'     => '%d & %d',
        'bitOr_2'      => '%d | %d',
        'bitNot_1'     => '~%d',
        'bitXor_2'     => '%d ^ %d'
      );

    public
      $escapeT      = '',
      $escapeTRules = array(),
      $escape       = '',
      $escapeRules  = array(),
      $dateFormat   = '';

    /**
     * get a function format string
     *
     * @param   SQLFunction func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction(SQLFunction $func) {
      $func_i= $func->func.'_'.sizeof($func->args);
      if (isset(self::$implementations[$func_i])) return self::$implementations[$func_i];
      throw new IllegalArgumentException('SQL function "'.$func->func.'()" not known');
    }

    /**
     * formats a string as date
     *
     * @param   string datestring
     * @return  string
     */
    public function formatDate($datestring) {
      return date($this->dateFormat, $datestring);
    }
    
    /**
     * escape a lable string
     *
     * @param   string escapeString
     * @return  string
     */
    public function escapeLabelString($escapeString) {
      return $this->quoteLabelString(strtr($escapeString, $this->escapeTRules));
    }
    
    /**
     * escape a lable string
     *
     * @param   string quoteString
     * @return  string
     */
    public function quoteLabelString($string) {
      return $this->escapeT.$string.$this->escapeT;
    }
    
    /**
     * escape a string
     *
     * @param   string escapeString
     * @return  string
     */
    public function escapeString($escapeString) {
      return $this->quoteString(strtr($escapeString, $this->escapeRules));
    }
    
    /**
     * escape a string
     *
     * @param   string quoteString
     * @return  string
     */
    public function quoteString($string) {
      return $this->escape.$string.$this->escape;
    }
    
    /**
     * get a dialect specific datepart
     *
     * @param   string datepart
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function datepart($datepart) {
      $datepart= strtolower($datepart);
      if (!array_key_exists($datepart, self::$dateparts)) throw new IllegalArgumentException('datepart '.$datepart.' does not exist');
      return self::$dateparts[$datepart];
    }

    /**
     * get a dialect specific datatype
     *
     * @param   string datatype
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function datatype($datatype) {
      $datatype= strtolower($datatype);
      if (!array_key_exists($datatype, self::$datatypes)) throw new IllegalArgumentException('datatype '.$datatype.' does not exist');
      return self::$datatypes[$datatype];
    }

    /**
     * Set date format
     *
     * @param   string format
     */
    public function setDateFormat($format) {
      $this->dateFormat= $format;
    }
    
    /**
     * Set date format
     *
     * @param   string[] rules
     */
    public function setEscapeRules($rules) {
      $this->escapeRules= $rules;
    }
    
    /**
     * Sets the escaping character.
     *
     * @param   string escape
     */
    public function setEscape($escape) {
      $this->escape= $escape;
    }

    /**
     * build join related part of an SQL query
     *
     * @param   rdbms.join.JoinRelation[] conditions
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    abstract public function makeJoinBy(Array $conditions);
  }
?>
