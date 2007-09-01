<?php
/* This class is part of the XP framework
 *
 * $Id: IOCollection.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace io::collections;

  ::uses('io.collections.IOElement');

  /**
   * IO Collection interface
   *
   * @purpose  Interface
   */
  interface IOCollection extends IOElement {

    /**
     * Open this collection
     *
     */
    public function open();

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     */
    public function rewind();
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @return  io.collection.IOElement
     */
    public function next();

    /**
     * Close this collection
     *
     */
    public function close();

  }
?>
