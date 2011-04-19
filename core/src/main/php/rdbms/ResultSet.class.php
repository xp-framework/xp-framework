<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.Date', 'rdbms.SQLException', 'lang.Closeable');

  /**
   * Result set as returned from the DBConnection::query method
   *
   * Usage (abbreviated example):
   * <code>
   *   // [...]
   *   $r= $conn->query('select news_id, caption, created_at from news');
   *   while ($row= $r->next()) {
   *     var_dump($row);
   *   }
   *   $r->close();
   *   // [...]
   * </code>
   *
   * @purpose  Resultset wrapper
   */
  class ResultSet extends Object implements Closeable {
    public
      $handle,
      $fields,
      $tz;

    /**
     * Constructor
     *
     * @param   resource handle
     * @param   array fields
     * @param   util.TimeZone tz default null
     */
    public function __construct($handle, $fields, TimeZone $tz= NULL) {
      $this->handle= $handle;
      $this->fields= $fields;
      $this->tz= $tz;
    }

    /**
     * Seek to a specified position within the resultset
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { }

    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @param   string field default NULL
     * @return  var
     */
    public function next($field= NULL) { }

    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close() { }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->handle).')@'.xp::stringOf($this->fields);
    }
  }
?>
