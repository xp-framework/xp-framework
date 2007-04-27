<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 uses('rdbms.criterion.Projection');

 define('AVG',  'avg(%c)');
 define('SUM',  'sum(%c)');
 define('MIN',  'min(%c)');
 define('MAX',  'max(%c)');
 define('PROP', '%c');

  /**
   * belongs to the Criterion API
   *
   */
  class SimpleProjection extends Projection {
  
    protected
      $field= '',
      $command= '',
      $alias= '';

    /**
     * constructor
     *
     * @param  string fieldname
     * @param  string command form constlist
     * @param  string alias optional
     * @throws rdbms.SQLStateException
     */
    public function __construct($field, $command, $alias= '') {
      $this->field= $field;
      $this->command= $command;
      $this->alias= $alias;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql($conn, $types) {
      if (!is('rdbms.SQLFunction', $this->field) && !isset($types[$this->field])) throw new SQLStateException('field '.$field.' does not exist');
      return (0 == strlen($this->alias))
      ? $conn->prepare($this->command, $this->field)
      : $conn->prepare($this->command.' as %s', $this->field, $this->alias);
    }
  }
?>
