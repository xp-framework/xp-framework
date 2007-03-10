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
    public
      $position= array();

    /**
     * Creates a string representation of any
     *
     * @param   mixed a
     * @param   string indent default ''
     * @return  string
     */
    public static function stringOf($a, $indent= '') {
      if (is_array($a)) {
        if (is_int(key($a))) {    // Numeric arrays
          $s= "[\n";
          foreach ($a as $v) {
            $s.= $indent.'  - '.self::stringOf($v, $indent.'  ').",\n";
          }
          return $s.$indent.']';
        } else {                  // Hash maps
          $s= "{\n";
          foreach ($a as $k => $v) {
           $s.= $indent.'  - '.$k.' => '.self::stringOf($v, $indent.'  ').",\n";
         }
          return $s.$indent.'}';
        }
      } else if ($a instanceof self) {
        $s= $a->getClassName().'@(position= '.implode(', ', $a->position)."){\n";
        foreach (array_keys(get_class_vars(get_class($a))) as $key) {
          if ('_' != $key{0} && 'position' != $key) $s.= sprintf(
            "%s  [%-20s] %s\n", 
            $indent, 
            $key, 
            self::stringOf($a->{$key}, '  '.$indent)
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
     * @return  string
     */
    public function toString() {
      return self::stringOf($this);
    }
  }
?>
