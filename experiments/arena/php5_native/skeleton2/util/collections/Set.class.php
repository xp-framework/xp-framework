<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A set of objects
   *
   * @purpose  Interface
   */
  interface Set {
  
    /**
     * Adds an object
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    public function add(&$object);

    /**
     * Removes an object from this set
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if this set contained the specified element. 
     */
    public function remove(&$object);

    /**
     * Removes an object from this set
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if the set contains the specified element. 
     */
    public function contains(&$object);

    /**
     * Returns this set's size
     *
     * @access  public
     * @return  int
     */
    public function size();

    /**
     * Removes all of the elements from this set
     *
     * @access  public
     */
    public function clear();

    /**
     * Returns whether this set is empty
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty();

    /**
     * Adds an array of objects
     *
     * @access  public
     * @param   lang.Object[] objects
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    public function addAll($objects);

    /**
     * Returns an array containing all of the elements in this set. 
     *
     * @access  public
     * @return  lang.Object[] objects
     */
    public function toArray();

    /**
     * Returns a hashcode for this set
     *
     * @access  public
     * @return  string
     */
    public function hashCode();
    
    /**
     * Returns true if this set equals another set.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals(&$cmp);
  }
?>
