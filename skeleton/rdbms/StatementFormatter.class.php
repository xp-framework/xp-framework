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
   *   $formatter= new StatementFormatter();
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
      $dialect = NULL,
      $conn    = NULL;
  
    /**
     * constructor
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.SQLDialect dialect
     */
    public function __construct(DBConnection $conn, SQLDialect $dialect) {
      $this->conn= $conn;
      $this->dialect= $dialect;
    }
  
    /**
     * Embed the given arguments into the format string.
     *
     * @param   string fmt
     * @param   mixed[] args
     * @return  string
     * @throws  rdbms.SQLStateException if an error is encountered
     */
    public function format($fmt, $args) {
      static $tokens= 'cdefstul';
      
      $statement= '';
      $argumentOffset= 0;
      $offset= 0;
      $length= strlen($fmt)- 1;
      while (TRUE) {

        // Find next token (or end of string)
        $span= strcspn($fmt, '%"\'', $offset);
        $statement.= substr($fmt, $offset, $span);
        $offset+= $span;
        
        // If offset == length, it was the last token, so return
        if ($offset >= $length) return $statement;

        if ('"' === $fmt{$offset} || "'" === $fmt{$offset}) {

          // Escape string literals (which use double quote characters inside for escaping)
          $quote= $fmt{$offset};
          $strlen= $offset+ 1;
          do {
            $strlen+= strcspn($fmt, $quote, $strlen);
            if ($strlen >= $length || $quote !== $fmt{$strlen+ 1}) break;
            $strlen+= 2;
          } while (TRUE);

          if ($strlen > $length) {
            throw new SQLStateException('Unclosed string @offset '.$offset.': ...'.substr($fmt, $offset));
          }

          $statement.= $this->dialect->escapeString(
            strtr(substr($fmt, $offset+ 1, $strlen- $offset- 1), 
            array('%%' => '%', $quote.$quote => $quote)
          ));
          $offset= $strlen+ 1;
          continue;
        } else if (is_numeric($fmt{$offset+ 1})) {
        
          // Numeric argument type specifier, e.g. %1$s
          sscanf(substr($fmt, $offset), '%%%d$', $overrideOffset);
          $type= $fmt{$offset+ strlen($overrideOffset)+ 2};
          $offset+= strlen($overrideOffset)+ 3;
          if (!array_key_exists($overrideOffset- 1, $args)) {
            throw new SQLStateException('Missing argument #'.($overrideOffset- 1).' @offset '.$offset);
          }
          $argument= $args[$overrideOffset- 1];
        } else if (FALSE !== strpos($tokens, $fmt{$offset+ 1})) {
        
          // Known tokens
          $type= $fmt{$offset+ 1};
          $offset+= 2;
          if (!array_key_exists($argumentOffset, $args)) {
            throw new SQLStateException('Missing argument #'.$argumentOffset.' @offset '.$offset);
          }
          $argument= $args[$argumentOffset];
          $argumentOffset++;
        } else if ('%' == $fmt{$offset+ 1}) {
        
          // Escape sign
          $statement.= '%';
          $offset+= 2;
          continue;
        } else {
          throw new SQLStateException('Unknown token "'.$fmt{$offset+ 1}.'"');
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
      $traversable= is_array($var) ? $var : array($var);
      foreach ($traversable as $arg) {
        if (NULL === $arg) {
          $r.= 'NULL, '; 
          continue; 
        } else if ($arg instanceof Date) {
          $type= 's';
          $this->conn->tz && $arg= $this->conn->tz->translate($arg);
          $p= $arg->toString($this->dialect->dateFormat);
        } else if ($arg instanceof SQLRenderable) {
          $r.= $arg->asSql($this->conn).', ';
          continue;
        } else if ($arg instanceof Generic) {
          $p= $arg->toString();
        } else {
          $p= $arg;
        }

        switch ($type) {
          case 'c': $r.= $p; break; // plain source code
          case 'd': $r.= $this->numval($p); break; // digits
          case 'e': $r.= $this->dialect->datatype($p); break; // datatype name
          case 'f': $r.= $this->numval($p); break; // digit
          case 's': $r.= $this->dialect->escapeString($p); break; // string
          case 't': $r.= $this->dialect->datepart($p); break; // datepart name
          case 'u': $r.= $this->dialect->quoteString($this->dialect->formatDate($p)); break; // date
          case 'l': $r.= $this->dialect->escapeLabelString($p); break; // label
        }
        $r.= ', ';
      }

      return substr($r, 0, -2);
    }
    
    /**
     * Sets the SQL dialect.
     *
     * @param   rdbms.SQLDialect dialect
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
