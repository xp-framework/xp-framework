<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.NoSuchElementException');

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
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function newElement($name);

    /**
     * Creates a new collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function newCollection($name);

    /**
     * Finds an element inside this collection
     *
     * @param   string name
     * @return  io.collections.IOElement
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function findElement($name);
    
    /**
     * Finds a collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function findCollection($name);

    /**
     * Gets an element inside this collection
     *
     * @param   string name
     * @return  io.collections.IOElement
     * @throws  util.NoSuchElementException
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function getElement($name);
    
    /**
     * Get a collection inside this collection
     *
     * @param   string name
     * @return  io.collections.IOCollection
     * @throws  util.NoSuchElementException
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function getCollection($name);

    /**
     * Removes an element in this collection
     *
     * @param   string name
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function removeElement($name);

    /**
     * Removes a collection from this collection
     *
     * @param   string name
     * @throws  io.OperationNotSupportedException
     * @throws  io.IOException
     */
    public function removeCollection($name);
  }
?>
