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
   * Example: Setting a dialect for a connection
   * <code>
   *  SQLDialect::setDialect($myConnection, SQLDialect::MYSQL);
   * </code>
   *
   * Supported dialects:
   * <ul>
   *   <li>SQLDialect::MYSQL   - MySQL dialect</li>
   *   <li>SQLDialect::SYBASE  - Sybase dialect</li>
   * </ul>
   *
   * Other dialects can be registered via SQLDialect::registerDialect():
   * <code>
   *   SQLDialect::registerDialect(
   *     'hql', 
   *     XPClass::forName('com.example.hql.dialect.HQLDialect')
   *   );
   * </code>
   *
   * For all "hql://"-connections or any connections that have been
   * associated with this dialect via setDialect(), the HQLDialect 
   * class will be used.
   *
   * @purpose  Base class for all dialects 
   */
  abstract class SQLDialect extends Object {
    const MYSQL  = 'mysql';
    const SYBASE = 'sybase';
  
    private static
      $classes= array(
        self::MYSQL  => 'rdbms.dialect.MysqlDialect',
        self::SYBASE => 'rdbms.dialect.SybaseDialect',
      ),
      $instances= array(),
      $implementation= array(
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
        'dateadd_3'    => 'dateadd(%c, %d, %s)',
        'datename_2'   => 'datename(%c, %s)',
        'datepart_2'   => 'datepart(%c, %s)',
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
     * Set an SQL dialect for a connection  
     *
     * @param   rdbms.DBConnection conn
     * @param   string name
     * @throws  lang.ClassNotFoundException if there is no dialect by name "name"
     */
    public static function setDialect($conn, $name) {
      self::$instances[$name]= XPClass::forName(self::$classes[$name])->newInstance($conn);
    }
    
    /**
     * Register a dialect by name
     *
     * @param   string name
     * @param   lang.XPClass<rdbms.SQLDialect> dialect class
     * @throws  lang.IllegalArgumentException
     */
    public static function registerDialect($name, XPClass $dialectClass) {
      if (!$dialectClass->isSubclassOf('rdbms.SQLDialect')) {
        throw new IllegalArgumentException('Given argument must be a subclass of rdbms.SQLDialect');
      }
      self::$classes[$name]= $dialect->getName();
    }

    /**
     * Get an SQL dialect for a connection  
     *
     * @param   rdbms.DBConnection conn
     * @return  rdbms.SQLDialect
     */
    public static function getDialect($conn) {
      $scheme= $conn->dsn->url->getScheme();
      if (!isset(self::$instances[$scheme])) self::setDialect($conn, $scheme);
      return self::$instances[$scheme];
    }
    
    /**
     * get an SQL function string
     *
     * @param   string func
     * @param   mixed[] function arguments string or rdbms.SQLFunction
     * @return  string
     */
    public function renderFunction($func, $args) {
      $func_i= $func.'_'.sizeof($args);
      if (isset(self::$implementation[$func_i])) {
        array_unshift($args, self::$implementation[$func_i]);
        return call_user_func_array(array($this->conn, 'prepare'), $args);
      }
      throw new IllegalArgumentException('SQL function "'.$func.'()" not known');
    }

  }
?>
