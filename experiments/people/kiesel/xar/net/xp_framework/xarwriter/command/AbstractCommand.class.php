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
   * Base command class
   *
   * @purpose  Base command
   */
  class AbstractCommand extends Object {
    var
      $options    = 0,
      $filename   = '',
      $args       = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   int options
     * @param   string filename
     * @param   util.cmd.ParamString args
     */
    function __construct($options, $filename, $args) {
      $this->options= $options;
      $this->filename= $filename;
      $this->args= $args;
    }
    
    /**
     * Retrieve file arguments from commandline
     *
     * @access  public
     * @return  string[]
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
