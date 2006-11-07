<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: ActiveXObject.class.php 8321 2006-11-05 15:22:47Z friebe $
 */
 
  uses('ActiveXObject', 'ComClassLoader');
  
  /**
   * ActiveX class
   *
   * @ext      com
   * @see      http://www.webreference.com/js/column55/activex.html
   * @purpose  Factory
   */
  class ActiveX extends Object {
    var
      $_qname  = '',
      $_handle = NULL;
    
    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() {
      xp::registry('classloader.COM$', new ComClassLoader());
    }

    /**
     * Constructor
     *
     * @model   static
     * @access  public
     * @param   string class
     * @param   string server default NULL
     * @return  &ActiveXObject instance
     * @throws  lang.IllegalArgumentException if the automation object cannot be loaded
     */
    function &forName($class, $server= NULL) {
      static $proxies= array();

      if (!($handle= com_load($class, $server))) {
        return throw(new IllegalArgumentException('Cannot load '.$class.($server ? '@'.$server : '')));
      }
      
      $cl= &xp::registry('classloader.COM$');
      $proxy= 'COM·'.strtr($class, '.', '·');
      if (!isset($proxies[$proxy])) {
        $bytes= '{';
        $details= $cl->getClassDetails($handle);
        
        // Create property interceptors
        $bytes.= 'function __get($key, &$value) { $value= com_get($this->_handle, $key); return TRUE; }';
        $bytes.= 'function __set($key, &$value) { com_set($this->_handle, $key, $value); return TRUE; }';

        // Overwrite toString()
        $bytes.= sprintf(
          'function toString() { return \'ActiveXObject<%s>(%s$%s)\'; }',
          $class.($server ? '@'.$server : ''),
          $details['class'][DETAIL_ANNOTATIONS]['com']['name'],
          $details['class'][DETAIL_ANNOTATIONS]['com']['guid']
        );
        
        // Create proxy methods
        foreach ($details[1] as $method => $defines) {
          $bytes.= 'function '.$method.'(';
          foreach ($defines[DETAIL_ARGUMENTS] as $arg) {
            $bytes.= ('&' == $arg[1][0] ? '&' : '').'$'.$arg[0].', ';
          }
          $bytes= rtrim($bytes, ', ').') { return com_invoke($this->var, \''.$defines[DETAIL_NAME].'\', ';
          foreach ($defines[DETAIL_ARGUMENTS] as $arg) {
            $bytes.= '$'.$arg[0].', ';
          }
          $bytes= rtrim($bytes, ', ').'); }';
        }
        $bytes.= '}';
        
        $proxies[$proxy]= &$cl->defineClass($proxy, 'ActiveXObject', NULL, $bytes);
        xp::registry('classloader.'.$proxy, $cl);
      }
      
      $object= &$proxies[$proxy]->newInstance();
      $object->var= $handle;

      return $object;
    }
  }
?>

