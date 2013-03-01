<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ftp.server.storage.StorageCollection');

  /**
   * Memory storage used
   *
   * @see   xp://net.xp_framework.unittest.peer.ftp.TestingServer
   */
  class TestingCollection extends Object implements StorageCollection {

    /**
     * Deletes an entry
     *
     * @return  bool TRUE to indicate success
     */
    public function delete() {
      // TBI
    }

    /**
     * Renames an entry
     *
     * @param   string target
     * @return  bool TRUE to indicate success
     */
    public function rename($target) {
      // TBI
    }

    /**
     * Returns the filename including the path (relative to storage root)
     *
     * @return string
     */
    public function getFilename() {
      // TBI
    }

    /**
     * Retrieves the (short) name of a storage entry
     *
     * @return  string
     */
    public function getName() {
      // TBI
    }

    /**
     * Retrieves the owner's username
     *
     * @return  string
     */
    public function getOwner() {
      // TBI
    }

    /**
     * Retrieves the owner's group name
     *
     * @return  string
     */
    public function getGroup() {
      // TBI
    }

    /**
     * Retrieves the size of this storage entry
     *
     * @return  int bytes
     */
    public function getSize() {
      // TBI
    }

    /**
     * Retrieves the modified timestamp of this storage entry
     *
     * @return  int unix timestamp
     */
    public function getModifiedStamp() {
      // TBI
    }

    /**
     * Retrieves the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @see     http://www.google.com/search?ie=UTF8&q=Unix%20permissions
     * @return  int
     */
    public function getPermissions() {
      // TBI
    }

    /**
     * Sets the permissions of this storage entry expressed in a
     * unix-permission style integer
     *
     * @param   int permissions
     */
    public function setPermissions($permissions) {
      // TBI
    }

    /**
     * Retrieves the number of links
     *
     * @return  int
     */
    public function numLinks() {
      // TBI
    }

    /**
     * Retrieves a list of elements
     *
     * @return  peer.ftp.server.storage.StorageEntry[]
     */
    public function elements() {

    }

  }
?>