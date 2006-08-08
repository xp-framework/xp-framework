<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.FileUtil', 'io.File');

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
    
      // Create specific node class if not existant
      $out= &new File(dirname(__FILE__).'/nodes/'.$type.'Node.class.php');
      if (!$out->exists()) {
        $assignments= $members= $arguments= '';
        for ($i= 0; $i < sizeof($args); $i++) {
          $name= 'arg'.$i;
          $assignments.= '      $this->'.$name.'= $'.$name.";\n";
          $arguments.= '$'.$name.', ';
          $members.= '      $'.$name.",\n";
          $apidoc.= '     * @param   mixed '.$name."\n";
        }
        $src= sprintf('<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(\'net.xp_framework.tools.vm.VNode\');

  /**
   * %1$s
   *
   * @see   xp://net.xp_framework.tools.vm.nodes.VNode
   */ 
  class %1$sNode extends VNode {
    %2$s;
      
    /**
     * Constructor
     *
     * @access  public%3$s
     */
    function __construct(%4$s) {
%5$s
    }  
  }
?>',
          $type,
          $members ? "var\n".rtrim($members, ",\n") : '',
          $apidoc ? "\n".rtrim($apidoc, "\n") : '',
          rtrim($arguments, ', '),
          rtrim($assignments, "\n")
        );
        
        FileUtil::setContents($out, $src);
      }
      
      // Instantiate
      $n= &new PNode();
      $n->type= $type;
      foreach ($args as $arg) {
        $n->args[]= $arg;
      }

      // Console::writeLine('+ '.$n->toString());
      return $n;
    }
    
    function __sleep() {
      return array('type', 'args');
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
