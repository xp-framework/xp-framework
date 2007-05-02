<?php
/* This class is part of the XP framework
 *
 * $Id: StatementFormatter.class.php 9952 2007-04-11 08:54:00Z friebe $
 */
 
  /**
   * Formatter class for database queries.
   *
   * Example usage:
   * <code>
   *   $formatter= &new StatementFormatter();
   *   $this->formatter->setDialect(new AnysqlDialect());
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
    public
      $dialect      = NULL;
  
  
    /**
     * Embed the given arguments into the format string.
     *
     * @param   string fmt
     * @param   mixed[] args
     * @return  string
     */
    public function format($fmt, $args) {
      static $tokens= 'tsdcfu';
      
      $statement= '';
      $argumentOffset= 0;
      while (TRUE) {

        // Find next token (or end of string)
        $offset= strcspn($fmt, '%');
        $statement.= substr($fmt, 0, $offset);

        // If offset == length, it was the last token, so return
        if ($offset == strlen($fmt)) return $statement;
        
        if (is_numeric($fmt{$offset + 1})) {
        
          // Numeric argument type specifier, e.g. %1$s
          sscanf(substr($fmt, $offset), '%%%d$', $overrideOffset);
          $type= $fmt{$offset + strlen($overrideOffset) + 2};
          $fmt= substr($fmt, $offset + strlen($overrideOffset) + 3);
          if (!array_key_exists($overrideOffset - 1, $args)) {
            throw new SQLStateException('Missing argument #'.($overrideOffset - 1).' @offset '.$offset);
          }
          $argument= $args[$overrideOffset - 1];
        } else if (FALSE !== strpos($tokens, $fmt{$offset + 1})) {
        
          // Known tokens
          $type= $fmt{$offset + 1};
          $fmt= substr($fmt, $offset + 2);
          if (!array_key_exists($argumentOffset, $args)) {
            throw new SQLStateException('Missing argument #'.$argumentOffset.' @offset '.$offset);
          }
          $argument= $args[$argumentOffset];
          $argumentOffset++;
        } else if ('%' == $fmt{$offset + 1}) {
        
          // Escape sign
          $statement.= '%';
          $fmt= substr($fmt, $offset + 2);
          continue;
        } else {
        
          // Unknown tokens
          $statement.= '%'.$fmt{$offset + 1};
          $fmt= substr($fmt, $offset + 2);
          continue;
        }
        
        $statement.= $this->prepare($type, $argument);
      }
    }
    
    /**
     * Prepare a value for insertion with a given type.
     *
     * @param   string type
     * @param   mixed var
     * @return  string
     */
    public function prepare($type, $var) {
      $r= '';
      foreach (is_array($var) ? $var : array($var) as $arg) {
        // Type-based conversion
        if (NULL === $arg) {
          $r.= 'NULL, '; 
          continue; 
        } else if ($arg instanceof Date) {
          $type= 's';
          $p= $arg->toString($this->dialect->dateFormat);
        } else if ($arg instanceof SQLFunction) {   // TODO: Use interface "SQLChunk"...
          $r.= $this->format($this->dialect->formatFunction($arg), $arg->args).', ';
          continue;
        } else if ($arg instanceof Generic) {
          $p= $arg->toString();
        } else {
          $p= $arg;
        }

        switch ($type) {
          case 't': $r.= $this->dialect->datepart($p); break;
          case 's': $r.= $this->dialect->escapeString($p); break;
          case 'd': $r.= $this->numval($p); break;
          case 'c': $r.= $p; break;
          case 'f': $r.= $this->numval($p); break;
          case 'u': $r.= $this->dialect->escapeString($this->dialect->formatDate($p)); break;
        }
        $r.= ', ';
      }

      return substr($r, 0, -2);
    }
    
    /**
     * Sets the SQL dialect.
     *
     * @param   SQLDialect dialect
     */
    public function setDialect(SQLDialect $dialect) {
      $this->dialect= $dialect;
    }
    
    /**
     * Format a number
     *
     * @param   mixed arg
     * @return  string
     */
    public function numval($arg) {
      if (
        (0 >= sscanf($arg, '%[0-9.+-]%[eE]%[0-9-]', $n, $s, $e)) ||
        !is_numeric($n)
      ) return 'NULL';
        
      return $n.($e ? $s.$e : '');
    }
  }
?>
