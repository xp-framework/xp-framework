<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::join;
  uses('util.XPIterator');

  /**
   * Interface for joins
   * Classes, that implement this interface can be filled with objects
   * by a JoinProcessor
   *
   * @purpose rdbms.join
   * @see     xp://rdbms.join.JoinPart#extract
   */
  interface JoinExtractable {

    /**
     * set "in construct" result object
     *
     * @param   string role
     * @param   string unique key
     * @param   lang.Object obj
     */
    public function setCachedObj($role, $key, $obj);

    /**
     * get an object from "found objects"
     *
     * @param   string role
     * @param   string unique key
     * @throws  util.NoSuchElementException
     */
    public function getCachedObj($role, $key);

    /**
     * test an object for existance in "found objects"
     *
     * @param   string role
     * @param   string unique key
     */
    public function hasCachedObj($role, $key);

    /**
     * mark a role as chached
     *
     * @param   string role
     */
    public function markAsCached($role);
  }
?>
