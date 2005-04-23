<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('ClassIterator');

  /**
   *
   * @purpose  Base class for all others
   */
  class RootDoc extends Object {
    var
      $classes = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString options
     */
    function __construct(&$options) {
      $classes= array($options->value(1));
      $this->classes= &new ClassIterator($classes);
    }
  }
?>
