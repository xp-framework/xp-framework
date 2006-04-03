<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PNode extends Object {
    var
      $type= '',
      $args= array();
    
    function stringOf($a, $indent= '') {
      if (is_array($a)) {
        if (is_int(key($a))) {
          $s= "[\n";
          foreach ($a as $v) {
            $s.= $indent.'  - '.PNode::stringOf($v, $indent.'  ').",\n";
          }
          return $s.$indent.']';
        } else {
          $s= "{\n";
          foreach ($a as $k => $v) {
           $s.= $indent.'  - '.$k.' => '.PNode::stringOf($v, $indent.'  ').",\n";
         }
          return $s.$indent.'}';
       }
      }
      if (is_a($a, 'PNode')) {
        return str_replace("\n", "\n".$indent, $a->toString());
      }
      return xp::stringOf($a);
    }
    
    function &create($type, $args) {
      $n= &new PNode();
      $n->type= $type;
      
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if (is_a($args[$i], 'PNode')) {
          // $args[$i]->parent= $n;
          $n->args[$i]= $args[$i];
        } else {
          $n->args[$i]= $args[$i];
        }
      }

      // Console::writeLine('+ '.$n->toString());
      return $n;
    }
    
    function toString() {
      return (
        $this->type.'Node('.implode(', ', array_map(array(&$this, 'stringOf'), $this->args)).')'
        // '@{'.
        // ($this->parent ? 'parent= *->'.$this->parent->type.'Node' : 'top').
        // '}'
      );
    }
  }
?>
