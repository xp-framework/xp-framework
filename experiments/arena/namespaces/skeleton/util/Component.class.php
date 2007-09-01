<?php
/* This class is part of the XP framework
 *
 * $Id:$
 */

  namespace util;

  /**
   * Component is an interface for classes implementing the composite pattern.
   *
   * @see       xp://util.Visitor
   * @purpose   Interface
   */
  interface Component {

    /**
     * Takes a Visitor as argument and calls its 'visit()' method with the
     * instance of this object ($this) as argument
     *
     * @param   util.Visitor Visitor
     */
    public function accept($Visitor);

    /**
     * Adds the given component to the children
     *
     * @param   util.Component Component
     */
    public function addChild($Component);

    /**
     * Removes the given component from the children
     *
     * @param   util.Component Component
     */
    public function remChild($Component);

    /**
     * Returns an array with all children
     *
     * @return  util.Component[]
     */
    public function getChildren();

  }
?>
