<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'rdbms.SQLException');

  /**
   * Result set as returned from the DBConnection::query method
   *
   * Usage (abbreviated example):
   * <code>
   *   // [...]
   *   $r= &$conn->query('select news_id, caption, created_at from news');
   *   while ($row= $r->next()) {
   *     var_dump($row);
   *   }
   *   $r->close();
   *   // [...]
   * </code>
   *
   * @purpose  Resultset wrapper
   */
  class ResultSet extends Object {
    var
      $handle,
      $fields;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   resource handle
     * @param   array fields
     */
    function __construct($handle, $fields) {
      $this->handle= $handle;
      $this->fields= $fields;
    }
    
    /**
     * Seek to a specified position within the resultset
     * 
     * @model   abstract
     * @access  public
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    function seek($offset) { }

    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @model   abstract
     * @access  public
     * @param   string field default NULL
     * @return  mixed
     */
    function next($field= NULL) { }
    
    /**
     * Close resultset and free result memory
     *
     * @access  public
     * @return  bool success
     */
    function close() { }

  }
?>
