<?php
/* This class is part of the XP framework
 *
 * $Id: IOCollection.class.php 7944 2006-09-21 11:32:15Z friebe $ 
 */

  uses('io.collections.IOElement');

  /**
   * IO Collection interface
   *
   * @purpose  Interface
   */
  interface IOCollection {

    /**
     * Open this collection
     *
     * @access  public
     */
    public function open();

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     * @access  public
     */
    public function rewind();
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @access  public
     * @return  &io.collection.IOElement
     */
    public function &next();

    /**
     * Close this collection
     *
     * @access  public
     */
    public function close();

  }
?>
