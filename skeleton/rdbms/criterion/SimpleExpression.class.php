<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('IN',              'in (?)');
  define('NOT_IN',          'not in (?)');
  define('IS',              'is ?');
  define('IS_NOT',          'is not ?');
  define('LIKE',            'like ?');
  define('EQUAL',           '= ?');
  define('NOT_EQUAL',       '!= ?');
  define('LESS_THAN',       '< ?');
  define('GREATER_THAN',    '> ?');
  define('LESS_EQUAL',      '<= ?');
  define('GREATER_EQUAL',   '>= ?');
  define('BIT_AND',         ' & ? = ?');

  uses('rdbms.criterion.Criterion');

  /**
   * Simple expression
   *
   * @purpose  Criterion
   */
  class SimpleExpression extends Object implements Criterion {
    public
      $field  = '',
      $value  = NULL,
      $op     = '';

    /**
     * Constructor
     *
     * The operation may be one of:
     * <ul>
     *   <li>IN</li>
     *   <li>NOT_IN</li>
     *   <li>LIKE</li>
     *   <li>EQUAL</li>
     *   <li>NOT_EQUAL</li>
     *   <li>LESS_THAN</li>
     *   <li>GREATER_THAN</li>
     *   <li>LESS_EQUAL</li>
     *   <li>GREATER_EQUAL</li>
     * </ul>
     *
     * @param   string field
     * @param   mixed value
     * @param   string op default EQUAL
     */
    public function __construct($field, $value, $op= EQUAL) {
      static $nullMapping= array(
        EQUAL     => IS,
        NOT_EQUAL => IS_NOT
      );

      // Automatically convert '= NULL' to 'is NULL', former is not valid ANSI-SQL
      if (NULL === $value && isset($nullMapping[$op])) {
        $op= $nullMapping[$op];
      }
      $this->op= $op;
      $this->field= $field;
      $this->value= $value;
    }
    
    /**
     * Creates a string representation of this expression.
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s({%s %s} %% %s)',
        $this->getClassName(),
        xp::stringOf($this->field),
        $this->op,
        xp::stringOf($this->value)
      );
    }
  
    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @return  string
     */
    public function asSql(DBConnection $conn, Peer $peer) {
      $col= ($this->field instanceof Column) ? $this->field : $peer->column($this->field);
      return $col->asSQL($conn).' '.$conn->prepare(str_replace('?', $col->getType(), $this->op), $this->value);
    }

  } 
?>
