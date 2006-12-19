<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.IOElement');

  /**
   * IO Collection interface
   *
   * @purpose  Interface
   */
  interface IOCollection extends IOElement {

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
