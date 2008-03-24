<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Tree', 'util.NoSuchElementException');

  /**
   * Defines an interface for storing generated api documentation
   *
   * @purpose  Storage
   */
  interface DocStorage {
    
    /**
     * Stores an item
     *
     * @param   string name
     * @return  xml.Tree t
     */
    public function store($name, Tree $t);

    /**
     * Removes an item
     *
     * @param   string name
     * @throws  util.NoSuchElementException if no element by the given name exists
     */
    public function remove($name);
    
    /**
     * Gets an item
     *
     * @param   string name
     * @return  xml.Tree
     * @throws  util.NoSuchElementException if no element by the given name exists
     */
    public function get($name);

    /**
     * Checks whether an item exists
     *
     * @param   string name
     * @return  bool
     */
    public function contains($name);
  }
?>
