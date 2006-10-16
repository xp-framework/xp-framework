<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Extension filter
   *
   * @purpose  Iteration Filter
   */
  class ExtensionEqualsFilter extends Object {
    var
      $extension= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string extension the file extension to compare to
     */
    function __construct($extension) {
      $this->extension= '.'.ltrim($extension, '.');
    }
  
    /**
     * Accepts an element
     *
     * @access  public
     * @param   &io.collections.IOElement element
     * @return  bool
     */
    function accept(&$element) {
      return $this->extension == substr($element->getURI(), -1 * strlen($this->extension));
    }

    /**
     * Creates a string representation of this iterator
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'("'.$this->compare.'")';
    }
  
  } implements(__FILE__, 'io.collections.iterate.IterationFilter');
?>
