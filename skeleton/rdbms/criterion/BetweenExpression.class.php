<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Between expression
   *
   * @purpose  Criterion
   */
  class BetweenExpression extends Object {
    var
      $field  = '',
      $lo     = NULL,
      $hi     = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string field
     * @param   mixed lo
     * @param   mixed hi
     */
    function __construct($field, $lo, $hi) {
      $this->field= $field;
      $this->lo= &$lo;
      $this->hi= &$hi;
    }
  
    /**
     * Returns the fragment SQL
     *
     * @access  public
     * @param   &rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    function asSql(&$conn, $types) { 
      if (!isset($types[$this->field])) {
        return throw(new SQLStateException('Field "'.$this->field.'" unknown'));
      }

      return $this->field.' between '.$conn->prepare(
        $types[$this->field].' and '.$types[$this->field],
        $this->lo,
        $this->hi
      );
    }

  } implements(__FILE__, 'rdbms.criterion.Criterion');
?>
