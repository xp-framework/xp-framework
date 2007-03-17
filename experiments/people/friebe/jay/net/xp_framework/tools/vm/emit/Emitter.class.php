<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses(
    'net.xp_framework.tools.vm.CompileError', 
    'net.xp_framework.tools.vm.VNode',
    'util.log.Traceable'
  );
  
  /**
   * Emitter
   *
   * @purpose  Abstract base class
   */
  class Emitter extends Object implements Traceable {
    public
      $cat      = NULL,
      $position = array(),
      $filename = '',
      $errors   = array();

    /**
     * Emits an array of nodes
     *
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    public function emitAll($nodes) {
      foreach ($nodes as $node) $this->emit($node);
    }

    /**
     * Set Filename
     *
     * @param   string filename
     */
    public function setFilename($filename) {
      $this->filename= $filename;
    }

    /**
     * Get Filename
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }
    /**
     * Adds an error
     *
     * @param   net.xp_framework.tools.vm.CompileError error
     */
    public function addError($error) {
      $error->message.= ' at '.$this->filename.' line '.$this->position[0].' (offset '.$this->position[1].')';
      $this->errors[]= $error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @return  bool
     */
    public function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @return  net.xp_framework.tools.vm.CompileError[]
     */
    public function getErrors() {
      return $this->errors;
    }
    
    /**
     * Retrieves result 
     *
     * @return  string
     */
    public function getResult() { }

    /**
     * Emits a single node
     *
     * @param   net.xp_framework.tools.vm.VNode node
     */
    public function emit($node) {
      if ($node instanceof VNode) {
        $this->position= $node->position;

        $func= 'emit'.ucfirst(substr(get_class($node), 0, -4));
        if (!method_exists($this, $func)) {
          $this->cat && $this->cat->error('No emitter for', $node);
          $this->addError(new CompileError(100, 'No emitter for '.$node->getClassName()));
          return;
        }

        $this->cat && $this->cat->debug($func, '(', $node->getClassName(), ')');
        $this->$func($node);
        return;
      } else if (is_array($node)) {
        $this->emitBlock($node);
        return;
        
      // -- FIXME: These should all be nodes! --------------------------------------------
      } else if ('"' == $node{0}) { // Double-quoted string
        $value= '';
        for ($i= 1, $s= strlen($node)- 1; $i < $s; $i++) {
          if ('\\' == $node{$i}) {
            switch ($node{$i+ 1}) {
              case 'r': $value.= "\r"; break;
              case 'n': $value.= "\n"; break;
              case 't': $value.= "\t"; break;
              case '"': $value.= '"'; break;

              case 'x': {   // \x[0-9A-Fa-f]{1,2}, TBD: Checks needed? \xGG will break...
                $length= min(strspn($node, '0123456789abdefABCDEF', $i+ 2), 2);
                $value.= chr(hexdec(substr($node, $i+ 2, $length)));
                $i+= $length;
                break;
              }
              
              case '0':     // \[0-7]{1,3}
              case '1':
              case '2':
              case '3':
              case '4':
              case '5':
              case '6':
              case '7': {
                $length= min(strspn($node, '01234567', $i+ 1), 3);
                $value.= chr(octdec(substr($node, $i+ 1, $length)));
                $i+= $length- 1;
                break;
              }
            }
            $i++;
          } else {
            $value.= $node{$i};
          }
        }
        $this->emitString($value);
        return;
      } else if ("'" == $node{0}) { // Single-quoted string
        $this->emitString(str_replace("\'", "'", substr($node, 1, -1)));
        return;
      } else if (is_int($node)) {
        $this->emitInteger($node);
        return;
      } else if (is_string($node)) {
        $this->emitConstant($node);
        return;
      }

      $this->cat && $this->cat->error('Cannot handle', $node);
    }

    /**
     * Emits a block
     *
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    public function emitBlock($nodes) { }

    /**
     * Emits constants
     *
     * @param   string name
     */
    public function emitConstant($name) { }

    /**
     * Emits constants
     *
     * @param   string string
     */
    public function emitString($string) { }

    /**
     * Emits an integer
     *
     * @param   int integer
     */
    public function emitInteger($integer) { }
    
    /**
     * Set a logger category for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

  }
?>
