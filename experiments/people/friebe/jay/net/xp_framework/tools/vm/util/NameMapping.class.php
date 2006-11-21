<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Maps names and types
   *
   * @purpose  Utility
   */
  class NameMapping extends Object {
    var
      $mapping= array(
        'xp' => 'xp',
        'parent' => 'parent',
        'self' => 'self',
      ),
      $current= '',
      $namespaceSeparator= '.';
    
    /**
     * Set namespace separator
     *
     * @access  public
     * @param   string s new value
     */
    function setNamespaceSeparator($s) {
      $this->namespaceSeparator= $s;
    }
    
    /**
     * Add a mapping
     *
     * @access  public
     * @param   string key
     * @param   string value
     */
    function addMapping($key, $value) {
      $this->mapping[$key]= $value;
    }

    /**
     * Retrieve a mapping
     *
     * @access  public
     * @param   string key
     * @return  string value
     * @throws  lang.IllegalArgumentException in case not mapping can be found
     */
    function getMapping($key) {
      if (!isset($this->mapping[$key])) {
        return throw(new IllegalArgumentException('Mapping for "'.$key.'" not found'));
      }
      
      return $this->mapping[$key];
    }
    
    /**
     * Set current class
     *
     * @access  public
     * @param   string c
     */
    function setCurrentClass($c) {
      $this->current= $c;
    }
    
    /**
     * Retrieves qualified name of a given short name
     *
     * @access  public
     * @param   string
     * @return  string
     * @throws  lang.IllegalArgumentException in case not mapping can be found
     */
    function qualifiedNameOf($short) {
      $mapped= $this->getMapping(strtolower($short));      
      return ($this->current == $mapped ? 'self' : $mapped);
    }

    /**
     * Retrieves packaged name of a given qualified name
     *
     * @access  public
     * @param   string q qualified class name
     * @return  string
     */
    function packagedNameOf($q) {
      if (strstr($q, '.')) {
        $packaged= strtr($q, '.', $this->namespaceSeparator);
      } else {
        $packaged= $q;
      }
      return $packaged;
    }
    
    
    /**
     * Retrieves type name
     *
     * @access  public
     * @param   string type
     * @param   bool arg default FALSE
     * @return  string
     */
    function forType($type, $arg= FALSE) {
      static $map= array( // Migrate gettype() style names to var_dump() style names
        'integer'       => 'int',
        'double'        => 'float',
        'boolean'       => 'bool',
      );
      static $builtin= array(
        'int'     => TRUE,
        'float'   => TRUE,
        'bool'    => TRUE,
        'string'  => TRUE,
        'array'   => TRUE,
        'mixed'   => TRUE
      );

      $va= ('*' == substr($type, -1) && $arg) ? '...' : '';
      $array= (!$va && '[]' == substr($type, -2)) ? '[]' : '';

      if (FALSE !== ($generic= strpos($type, '<'))) {
        $type= substr($type, 0, $generic);
      }
      
      $type= trim($type, '&[]*');
      $lookup= strtolower($type);
      if (isset($map[$lookup])) $type= $map[$lookup];
      
      if (!isset($builtin[$type])) {    // User-defined
        if (strstr($type, '.')) {
          $type= $this->packagedNameOf($type);
        } else {
          $type= $this->packagedNameOf($this->qualifiedNameOf($type));
        }
      }
      
      return $type.$va.$array;
    }
  }
?>
