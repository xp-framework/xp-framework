<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  uses(
    'net.xp_framework.tools.vm.CompileError', 
    'net.xp_framework.tools.vm.VNode'
  );
  
  /**
   * Emitter
   *
   * @purpose  Abstract base class
   */
  class Emitter extends Object {
    var
      $cat      = NULL,
      $position = array(),
      $filename = '',
      $errors   = array();

    /**
     * Emits an array of nodes
     *
     * @access  public
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    function emitAll($nodes) {
      foreach ($nodes as $node) $this->emit($node);
    }

    /**
     * Set Filename
     *
     * @access  public
     * @param   string filename
     */
    function setFilename($filename) {
      $this->filename= $filename;
    }

    /**
     * Get Filename
     *
     * @access  public
     * @return  string
     */
    function getFilename() {
      return $this->filename;
    }
    /**
     * Adds an error
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.CompileError error
     */
    function addError(&$error) {
      $error->message.= ' at '.$this->filename.' line '.$this->position[0].' (offset '.$this->position[1].')';
      $this->errors[]= &$error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  bool
     */
    function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  net.xp_framework.tools.vm.CompileError[]
     */
    function getErrors() {
      return $this->errors;
    }
    
    /**
     * Retrieves result 
     *
     * @access  public
     * @return  string
     */
    function getResult() { }

    /**
     * Emits a single node
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.VNode node
     */
    function emit(&$node) {
      $this->position= $node->position;

      if (is_a($node, 'VNode')) {
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
     * @access  public
     * @param   net.xp_framework.tools.vm.VNode[] nodes
     */
    function emitBlock($nodes) { }

    /**
     * Emits constants
     *
     * @access  public
     * @param   string name
     */
    function emitConstant($name) { }

    /**
     * Emits constants
     *
     * @access  public
     * @param   string string
     */
    function emitString($string) { }

    /**
     * Emits an integer
     *
     * @access  public
     * @param   int integer
     */
    function emitInteger($integer) { }
    
    /**
     * Set a logger category for debugging
     *
     * @access  public
     * @param   util.log.LogCategory cat
     */
    function setTrace($cat) {
      $this->cat= $cat;
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
