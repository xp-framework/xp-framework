<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * TestCase for classloading
   */
  interface ClassFromUriBase {

    /**
     * Creates this base
     */
    public function create();

    /**
     * Returns the path for this URI base
     *
     * @return string
     */
    public function path();

    /**
     * Creates a new file (in this underlying base)
     *
     * @param  string $name
     * @param  string $contents
     */
    public function newFile($name, $contents);

    /**
     * Deletes this base
     */
    public function delete();
  }
?>