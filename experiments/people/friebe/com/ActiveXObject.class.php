<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  /**
   * ActiveXObject class
   *
   * @ext      com
   * @see      http://www.webreference.com/js/column55/activex.html
   * @purpose  COM Wrapper
   */
  class ActiveXObject extends Object {
    var
      $_qname  = '',
      $_handle = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string class
     * @param   string server default NULL
     * @throws  lang.IllegalArgumentException if the automation object cannot be loaded
     */
    function __construct($class, $server= NULL) {
      $this->_qname= $class.($server ? '@'.$server : '');
      if (!($this->_handle= com_load($class, $server))) {
        throw(new IllegalArgumentException('Cannot load '.$this->_qname));
      }
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      ob_start();
      com_print_typeinfo($this->_handle, NULL, FALSE);
      $buffer= ob_get_contents();
      ob_end_clean();
      
      sscanf($buffer, 'class %[^ ] { /* GUID={%[^}]}', $class, $guid);
      return $this->getClassName().'<'.$this->_qname.'>('.$class.'$'.$guid.')';
    }

    /**
     * Destructor. Releases the com automation handle.
     *
     * @access  public
     */
    function __destruct() {
      if ($this->_handle) {
        com_release($this->_handle);
        $this->_handle= NULL;
      }
    }
  }
?>
