<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * helps to build statments for different SQL servers
   *
   * @purpose  Base class for all dialects 
   */
  abstract class SQLDialect extends Object {
    private static
      $instances= array(),
      $dateparts= array(
        'YEAR'	      => 'YEAR',
        'QUARTER'	  => 'QUARTER',
        'MONTH'	      => 'MONTH',
        'DAYOFYEAR'	  => 'DAYOFYEAR',
        'DAY'	      => 'DAY',
        'WEEK'	      => 'WEEK',
        'WEEKDAY'	  => 'WEEKDAY',
        'HOUR'	      => 'HOUR',
        'MINUTE'	  => 'MINUTE',
        'SECOND'	  => 'SECOND',
        'MILLISECOND' => 'MILLISECOND',
        'MICROSECOND' => 'MICROSECOND',
      ),
      $implementations= array(
        'abs_1'        => 'abs(%d)',
        'acos_1'       => 'acos(%d)',
        'ascii_1'      => 'ascii(%s)',
        'asin_1'       => 'asin(%d)',
        'atan_1'       => 'atan(%d)',
        'atan_2'       => 'atan2(%d, %d)',
        'bit_length_1' => 'bit_length(%s)',
        'cast_2'       => 'cast(%s as %c)',
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
        'ltrim_2'      => 'trim(%s)',
        'ltrim_3'      => 'trim(%s, %s)',
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
        'sqrt_1'       => 'sqrt(%s)',
        'str_1'        => 'str(%c)',
        'substring_3'  => 'substring(%s, %s, %s)',
        'tan_1'        => 'tan(%d)',
        'trim_2'       => 'trim(%s)',
        'trim_3'       => 'trim(%s, %s)',
        'upper_1'      => 'upper(%s)',
        'year_1'       => 'year(%s)',
      );

    public
      $escape       = '',
      $escapeRules  = array(),
      $dateFormat   = '';

    /**
     * get a function format string
     *
     * @param   SQLFunction $func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction(SQLFunction $func) {
      $func_i= $func->func.'_'.sizeof($func->args);
      if (isset(self::$implementations[$func_i])) return self::$implementations[$func_i];
      throw new IllegalArgumentException('SQL function "'.$func.'()" not known');
    }

    /**
     * get a dialect specific datepart
     *
     * @param   string datepart
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function datepart($datepart) {
      if (!array_key_exists($datepart, self::$dateparts)) throw new IllegalArgumentException('datepart '.$datepart.' does not exist');
      return self::$dateparts[$datepart];
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
     * @param   array<String,String> rules
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
     * escape a string
     *
     * @param   string
     * @return  string
     */
    public function escapeString($string) {
      return $this->escape.strtr($string, $this->escapeRules).$this->escape;
    }
    
    /**
     * formats a string as date
     *
     * @param   string
     * @return  string
     */
    public function formatDate($string) {
      return date($this->dateFormat, $string);
    }
    
  }
?>
