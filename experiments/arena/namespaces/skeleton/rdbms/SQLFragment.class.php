<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  namespace rdbms;

  uses('rdbms.SQLRenderable');

  /**
   * Represents a query fragment to be used in a Criteria query
   *
   * @purpose  Interface
   */
  interface SQLFragment extends SQLRenderable {
  
    /**
     * Get the type this fragment evaluates to. Returns one of the %-tokens
     * to be used in prepare() (e.g. %s, %c, %u, ...)
     *
     * @return  string
     */
    public function getType();

  }
?>
