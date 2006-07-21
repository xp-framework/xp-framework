<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.doclet.Doc');

  /**
   *
   * @purpose  Documents a method
   */
  class MethodDoc extends Doc {
    public
      $annotations  = array(),
      $arguments    = array();
      
  }
?>
