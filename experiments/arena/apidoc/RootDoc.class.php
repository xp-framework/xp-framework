<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('ClassIterator');
  
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
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString options
     */
    function __construct(&$options) {
      $classes= array();
      
      // Separate options from classes
      $valid= $this->validOptions();
      for ($i= 1; $i < $options->count; $i++) {
        $option= &$options->list[$i];
        
        if (0 == strncmp($option, '--', 2)) {        // Long: --foo / --foo=bar
          $p= strpos($option, '=');
          $name= substr($option, 2, FALSE === $p ? strlen($option) : $p- 2);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $this->options[$name]= FALSE === $p ? NULL : substr($option, $p+ 1);
            } else {
              $this->options[$name]= TRUE;
            }
          }
        } elseif (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
          $name= substr($option, 1);
          if (isset($valid[$name])) {
            if ($valid[$name] == HAS_VALUE) {
              $this->options[$name]= $options->list[++$i];
            } else {
              $this->options[$name]= TRUE;
            }
          }          
        } else {
          $classes[]= $option;
        }
      }
      
      // Set up class iterator
      $this->classes= &new ClassIterator($classes);
    }
    
    /**
     * Return a list of valid options as an associative array, keys
     * forming parameter names and values defining whether this option
     * expects a value.
     *
     * Example:
     * <code>
     *   return array(
     *     'classpath' => HAS_VALUE,
     *     'verbose'   => OPTION_ONLY
     *   );
     * </code>
     *
     * Returns an empty array in this default implementation.
     *
     * @access  public
     * @return  array
     */
    function validOptions() {
      return array();
    }
  }
?>
