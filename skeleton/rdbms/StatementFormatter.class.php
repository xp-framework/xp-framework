<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Formatter class for database queries.
   *
   * Example usage:
   * <code>
   *   $formatter= &new StatementFormatter();
   *   $formatter->setEscapeRules(array(
   *     '"'   => '""',
   *     '\\'  => '\\'
   *   ));
   *   $formatter->setDateFormat('Y-m-d h:iA');
   *   $formatter->format('select foo from table where id= %d', 123);
   * </code>
   *
   * @test    xp://net.xp_framework.unittest.rdbms.TokenizerTest
   * @see     xp://rdbms.sybase.SybaseConnection
   * @see     xp://rdbms.mysql.MysqlConnection
   * @see     xp://rdbms.pgsql.PostgresqlConnection
   * @purpose Format database query strings
   */
  class StatementFormatter extends Object {
    var
      $escapeRules    = array(
        '"'   => '""',
        '\\'  => '\\'
      ),
      $dateFormat=      'Y-m-d h:iA';
  
  
    /**
     * Embed the given arguments into the format string.
     *
     * @access  public
     * @param   string fmt
     * @param   mixed[] args
     * @return  string
     */
    function format($fmt, $args) {
      $statement= '';
      
      $argumentOffset= 0;
      while (TRUE) {

        // Find next token (or end of string)
        $offset= strcspn($fmt, '%');
        $statement.= substr($fmt, 0, $offset);
        
        // If offset == length, it was the last token, so return
        if ($offset == strlen($fmt)) return $statement;
        
        if (is_numeric($fmt{$offset + 1})) {
          sscanf(substr($fmt, $offset), '%%%d$', $overrideOffset);
          $type= $fmt{$offset + strlen($overrideOffset) + 2};
          $fmt= substr($fmt, $offset + strlen($overrideOffset) + 3);
          $argument= isset($args[$overrideOffset - 1]) ? $args[$overrideOffset - 1] : NULL;
        } else {
          $type= $fmt{$offset + 1};
          $fmt= substr($fmt, $offset + 2);
          $argument= isset($args[$argumentOffset]) ? $args[$argumentOffset] : NULL;
          $argumentOffset++;
        }
        
        $statement.= $this->prepare($type, $argument);
      }
    }
    
    /**
     * Prepare a value for insertion with a given type.
     *
     * @access  public
     * @param   string type
     * @param   mixed var
     * @return  string
     */
    function prepare($type, $var) {
      // Type-based conversion
      if (is_a($var, 'Date')) {
        $type= 's';
        $a= array($var->toString($this->dateFormat));
      } elseif (is_a($var, 'Object')) {
        $a= array($var->toString());
      } elseif (is_array($var)) {
        $a= $var;
      } else {
        $a= array($var);
      }

      $r= '';
      foreach ($a as $arg) {
        if (NULL === $arg) { $r.= 'NULL, '; continue; }
        switch ($type) {
          case 's': $r.= '"'.strtr($arg, $this->escapeRules).'"'; break;
          case 'd': $r.= sprintf('%.0f', $arg); break;
          case 'c': $r.= $arg; break;
          case 'f': $r.= floatval($arg); break;
          case 'u': $r.= '"'.date($this->dateFormat, $arg).'"'; break;
          case '%': $r.= '%'; break;
          default: $r.= '%'.$type;
        }
        $r.= ', ';
      }

      return substr($r, 0, -2);
    }
    
    /**
     * Set date format
     *
     * @access  public
     * @param   string format
     */
    function setDateFormat($format) {
      $this->dateFormat= $format;
    }
    
    /**
     * Set date format
     *
     * @access  public
     * @param   array<String,String> rules
     */
    function setEscapeRules($rules) {
      $this->escapeRules= $rules;
    }
  }
?>
