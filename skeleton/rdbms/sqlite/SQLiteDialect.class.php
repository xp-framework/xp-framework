<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLDialect');

  /**
   * helps to build functions for different SQL servers
   *
   */
  class SQLiteDialect extends SQLDialect {
    public
      $escape       = "'",
      $escapeRules  = array("'"  => "''"),
      $escapeT      = "'",
      $escapeRulesT = array("'"  => "''"),
      $dateFormat   = 'Y-m-d H:i:s';
        
    private static
      $dateparts= array(
        'day'         => '"d"',
        'dayofyear'   => '"z"',
        'hour'        => '"H"',
        'microsecond' => FALSE,
        'millisecond' => FALSE,
        'minute'      => '"i"',
        'month'       => '"m"',
        'quarter'     => FALSE,
        'second'      => 's',
        'week'        => FALSE,
        'weekday'     => FALSE,
        'year'        => 'Y',
      ),
      $implementations= array(
        'abs_1'        => 'php("abs", %d)',
        'acos_1'       => 'php("acos", %d)',
        'ascii_1'      => 'php("ord", %s)',
        'asin_1'       => 'php("asin", %d)',
        'atan_1'       => 'php("atan", %d)',
        'atan_2'       => 'php("atan2", %d, %d)',
        'bit_length_1' => 'bit_length_not_implemented',
        'cast_2'       => 'cast(%s, "%e")',
        'ceil_1'       => 'php("ceil", %d)',
        'char_1'       => 'php("chr", %d)',
        'cos_1'        => 'php("cos", %d)',
        'cot_1'        => 'php("tan", php("pi") / 2 - %d)',
        'dateadd_3'    => 'dateadd(%t, %d, %s)',
        'datediff_3'   => 'datediff_not_implemented',
        'datename_2'   => 'php("strval", php("idate", %t, php("strtotime", %s)))',
        'datepart_2'   => 'php("idate", %t, php("strtotime", %s))',
        'day_1'        => 'php("idate", "d", php("strtotime", %s))',
        'degrees_1'    => 'php("rad2deg", %d)',
        'exp_1'        => 'php("exp", %d)',
        'floor_1'      => 'php("floor", %d)',
        'getdate_0'    => 'php("date", "Y-m-d H:i:s", php("time"))',
        'hour_1'       => 'php("idate", "H", php("strtotime", %s))',
        'len_1'        => 'php("strlen", %s)',
        'length_1'     => 'php("strlen", %s)',
        'locate_2'     => 'locate(%s, %s, 0)',
        'locate_3'     => 'locate(%s, %s, %d)',
        'log10_1'      => 'php("log10", %d)',
        'log_1'        => 'php("log", %d)',
        'lower_1'      => 'php("strtolower, "%s)',
        'ltrim_1'      => 'php("ltrim", %s)',
        'ltrim_2'      => 'php("ltrim", %s, %s)',
        'minute_1'     => 'php("idate", "i", php("strtotime", %s))',
        'month_1'      => 'php("idate", "m", php("strtotime", %s))',
        'nullif_2'     => 'nullif(%s, %s)',
        'pi_0'         => 'php("pi")',
        'power_2'      => 'php("pow", %d, %d)',
        'radians_1'    => 'php("deg2rad", %d)',
        'rand_0'       => 'php("rand")',
        'reverse_1'    => 'php("strrev", %s)',
        'round_2'      => 'php("round", %d, %d)',
        'rtrim_1'      => 'php("rtrim", %s)',
        'rtrim_2'      => 'php("rtrim", %s, %s)',
        'second_1'     => 'php("idate", "s", php("strtotime", %s))',
        'sign_1'       => 'sign(%d)',
        'sin_1'        => 'php("sin", %d)',
        'soundex_1'    => 'php("soundex", %s)',
        'space_1'      => 'php("str_repeat", " ", %d)',
        'sqrt_1'       => 'php("sqrt", %d)',
        'str_1'        => 'php("strval", %s)',
        'substring_3'  => 'php("substr", %s, %d, %d)',
        'substring_2'  => 'php("substr", %s, %d)',
        'tan_1'        => 'php("tan", %d)',
        'trim_2'       => 'php("trim", %s)',
        'trim_3'       => 'php("trim", %s, %s)',
        'upper_1'      => 'php("strtoupper", %s)',
        'year_1'       => 'php("idate", "Y", php("strtotime", %s))',
      );

    /**
     * register sql standard functions for a connection
     *
     * @param   db handel conn
     */
    function registerCallbackFunctions($conn) {
      sqlite_create_function($conn, 'cast', array($this, '_cast'), 2);
      sqlite_create_function($conn, 'sign', array($this, '_sign'), 1);
      sqlite_create_function($conn, 'dateadd', array($this, '_dateadd'), 3);
      sqlite_create_function($conn, 'locate',  array($this, '_locate'), 3);
      sqlite_create_function($conn, 'nullif',  array($this, '_nullif'), 2);
    }

    /**
     * Callback function to cast data
     *
     * @param   mixed s
     * @param   mixed type
     * @return  mixed
     */
    public function _cast($s, $type) {
      static $identifiers= array(
        'bigint'     => "\3",
        'date'       => "\2",
        'datetime'   => "\2",
        'decimal'    => "\4",
        'double'     => "\4",
        'float'      => "\4",
        'int'        => "\3",
        'integer'    => "\3",
        'smallint'   => "\3",
      );
      return is_null($s) ? NULL : $identifiers[strtolower($type)].$s;
    }

    /**
     * Callback function to compare to statements
     *
     * @param   string arg1
     * @param   string arg2
     * @return  int
     */
    public function _nullif($arg1, $arg2) {
      if ($arg1 == $arg2) return NULL;
      return $arg1;
    }

    /**
     * Callback function to find a string in a string
     *
     * @param   string haystack
     * @param   string needle
     * @param   int start
     * @return  int
     */
    public function _locate($h, $n, $s) {
      if (is_null($h) or is_null($n))  return NULL;
      return intval(strpos($h, $n, $s));
    }

    /**
     * Callback function to find the signature of a float
     *
     * @param   float dig
     * @return  int
     */
    public function _sign($dig) {
      $dig= floatval($dig);
      if ($dig > 0) return 1;
      if ($dig < 0) return -1;
      return 0;
    }

    /**
     * Callback function add a datepart to a date
     *
     * @param   sring datepart
     * @param   int amount to add
     * @param   string datestr
     * @return  string
     */
    public function _dateadd($part, $amount, $datestr) {
      $part= current(array_keys(self::$dateparts, '"'.$part.'"'));
      $date= new DateTime($datestr);
      $date->modify($amount.' '.$part);
      return $date->format($this->dateFormat);
    }

    /**
     * get a function format string
     *
     * @param   SQLFunction func
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function formatFunction(SQLFunction $func) {
      $func_i= $func->func.'_'.sizeof($func->args);
      switch ($func->func) {
        case 'concat':
        return implode(' || ', array_fill(0, sizeof($func->args), '%s'));

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
      if (FALSE === self::$dateparts[$datepart]) throw new IllegalArgumentException('PostgreSQL does not support datepart '.$datepart);
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
      if (0 == sizeof($conditions)) throw new IllegalArgumentException('conditions can not be empty');
      $querypart= '';
      $first= TRUE;
      foreach ($conditions as $link) {
        if ($first) {
          $first= FALSE;
          $querypart.= sprintf(
            '%s LEFT OUTER JOIN %s on (%s) ',
            $link->getSource()->toSqlString(),
            $link->getTarget()->toSqlString(),
            implode(' and ', $link->getConditions())
          );
        } else {
          $querypart.= sprintf('LEFT JOIN %s on (%s) ', $link->getTarget()->toSqlString(), implode(' and ', $link->getConditions()));
        }
      }
      return $querypart.'where ';
    }
  }
?>
