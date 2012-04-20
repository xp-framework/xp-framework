<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Random access to an I/O collection
   *
   */
  interface RandomCollectionAccess {

    /**
     * Creates a new element in this collection
     *
     * @param   string name
     * @return  io.collections.IOElement
     */
    public function newElement($name);

    /**
     * Creates a new collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     */
    public function newCollection($name);

    /**
     * Finds an element inside this collection
     *
     * @param   string name
     * @return  io.collections.IOElement
     */
    public function findElement($name);

    /**
     * Finds a collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     */
    public function findCollection($name);

    /**
     * Gets an element inside this collection
     *
     * @param   string name
     * @return  io.collections.IOElement
     * @throws  util.NoSuchElementException
     */
    public function getElement($name);

    /**
     * Get a collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     * @throws  util.NoSuchElementException
     */
    public function getCollection($name);

    /**
     * Removes an element in this collection
     *
     * @param   string name
     */
    public function removeElement($name);

    /**
     * Removes a collection from this collection
     *
     * @param   string name
     */
    public function removeCollection($name);
  }
?>
