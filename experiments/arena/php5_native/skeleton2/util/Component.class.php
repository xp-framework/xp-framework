<?php
/* This class is part of the XP framework
 *
 * $Id:$
 */

  /**
   * Component is an interface for classes implementing the composite pattern.
   *
   * @see       xp://util.Visitor
   * @purpose   Interface
   */
  interface Component {
    
    public
      $_children= array();

    /**
     * Takes a Visitor as argument and calls its 'visit()' method with the
     * instance of this object ($this) as argument
     *
     * @access  public
     * @param   &util.Visitor Visitor
     */
    public function accept(&$Visitor);

    /**
     * Adds the given component to the children
     *
     * @access  public
     * @param   &util.Component Component
     */
    public function addChild(&$Component);

    /**
     * Removes the given component from the children
     *
     * @access  public
     * @param   &util.Component Component
     */
    public function remChild(&$Component);

    /**
     * Returns an array with all children
     *
     * @access  public
     * @return  &util.Component[]
     */
    public function getChildren();

  }
?>
