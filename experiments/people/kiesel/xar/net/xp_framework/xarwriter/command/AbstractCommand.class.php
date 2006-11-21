<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.archive.Archive',
    'io.File',
    'io.FileUtil',
    'io.Folder'
  );
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractCommand extends Object {
    var
      $options    = 0,
      $filename   = '',
      $args       = array();
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($options, $filename, $args) {
      $this->options= $options;
      $this->filename= $filename;
      $this->args= $args;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getArguments() {
      $args= array();
      
      for ($i= 3; $i < $this->args->count; $i++) {
        $a= $this->args->value($i);
        
        if (0 == strncmp('--', $a, 2)) continue;
        
        // It's a short option with a following parameter
        if ('-' == $a{0}) {
          $i+= 1;
          continue;
        }
        
        $args[]= $a;
      }
      
      return $args;
    }
  }
?>
