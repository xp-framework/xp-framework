<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Base class for all other nodes
   *
   */
  class VNode extends Object {
    var
      $position= array();

    /**
     * Creates a string representation of any
     *
     * @model   static
     * @access  public
     * @param   mixed a
     * @param   string indent default ''
     * @return  string
     */
    function stringOf($a, $indent= '') {
      if (is_array($a)) {
        if (is_int(key($a))) {    // Numeric arrays
          $s= "[\n";
          foreach ($a as $v) {
            $s.= $indent.'  - '.VNode::stringOf($v, $indent.'  ').",\n";
          }
          return $s.$indent.']';
        } else {                  // Hash maps
          $s= "{\n";
          foreach ($a as $k => $v) {
           $s.= $indent.'  - '.$k.' => '.VNode::stringOf($v, $indent.'  ').",\n";
         }
          return $s.$indent.'}';
        }
      } else if (is_a($a, 'VNode')) {
        $s= $a->getClassName().'@(position= '.implode(', ', $a->position)."){\n";
        foreach (array_keys(get_class_vars(get_class($a))) as $key) {
          if ('_' != $key{0} && 'position' != $key) $s.= sprintf(
            "%s  [%-20s] %s\n", 
            $indent, 
            $key, 
            VNode::stringOf($a->{$key}, '  '.$indent)
          );
        }
        return $s.$indent.'}';
      } else {
        return xp::stringOf($a);
      }
    }

    /**
     * Creates a string representation of this node
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return VNode::stringOf($this);
    }
  }
?>
