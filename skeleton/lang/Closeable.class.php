<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates an object is closeable. Used in the XP language's ARM
   * statement. The close method is invoked to release previously 
   * acquired resources, e.g. an open file, or a database connection.
   *
   */
  interface Closeable {

    /**
     * Closes this object. May be called more than once, which may
     * not fail - that is, if the object is already closed, this 
     * method should have no effect.
     *
     * @throws  lang.XPException
     */
    public function close(); 
  }
?>
