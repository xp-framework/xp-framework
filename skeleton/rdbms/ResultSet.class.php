<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

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
      $this->handle=  $handle;
      $this->fields=  $fields;
      parent::__construct();
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
     * Fetch a row (iterator function)
     *
     * @model   abstract
     * @access  public
     * @return  array rowset or FALSE to indicate all rows have been fetched
     */
    function next() { }
    
    /**
     * Close resultset and free result memory
     *
     * @access  public
     * @return  bool success
     */
    function close() { }

  }
?>
