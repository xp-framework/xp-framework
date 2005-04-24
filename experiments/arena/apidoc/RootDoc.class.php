<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('ClassIterator', 'util.cmd.ParamString');
  
  define('OPTION_ONLY', 0x0000);
  define('HAS_VALUE',   0x0001);

  /**
   *
   * @purpose  Base class for all others
   */
  class RootDoc extends Object {
    var
      $classes = NULL,
      $options = array();
    
    /**
     * Start a doclet
     *
     * @model   static
     * @access  public
     * @param   &Doclet doclet
     * @param   &util.cmd.ParamString params
     * @return  bool
     */
    function start(&$doclet, &$params) {
      $classes= array();
      $root= &new RootDoc();
      
      // Separate options from classes
      $valid= $doclet->validOptions();
      for ($i= 1; $i < $params->count; $i++) {
        $option= &$params->list[$i];
        
        if (0 == strncmp($option, '--', 2)) {        // Long: --foo / --foo=bar
          $p= strpos($option, '=');
          $name= substr($option, 2, FALSE === $p ? strlen($option) : $p- 2);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $root->options[$name]= FALSE === $p ? NULL : substr($option, $p+ 1);
            } else {
              $root->options[$name]= TRUE;
            }
          }
        } elseif (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
          $name= substr($option, 1);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $root->options[$name]= $params->list[++$i];
            } else {
              $root->options[$name]= TRUE;
            }
          }          
        } else {
          $classes[]= $option;
        }
      }
      
      // Set up class iterator
      $root->classes= &new ClassIterator($classes);

      // Start the doclet
      return $doclet->start($root);
    }
    
    /**
     * Returns an option by a given name or the specified default value
     * if the option does not exist.
     *
     * @access  public
     * @param   string name
     * @param   string default default NULL
     * @return  string
     */
    function option($name, $default= NULL) {
      return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
  }
?>
