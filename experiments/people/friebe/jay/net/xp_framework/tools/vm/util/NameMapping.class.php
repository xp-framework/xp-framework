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
    public
      $mapping= array(
        'xp'      => 'xp',
        'parent'  => 'parent',
        'self'    => 'self',
      ),
      $current= NULL,
      $namespaceSeparator= '.';
    
    /**
     * Set namespace separator
     *
     * @param   string s new value
     */
    public function setNamespaceSeparator($s) {
      $this->namespaceSeparator= $s;
    }
    
    /**
     * Add a mapping
     *
     * @param   string key
     * @param   string value
     */
    public function addMapping($key, $value) {
      $this->mapping[$key]= $value;
    }

    /**
     * Retrieve a mapping
     *
     * @param   string key
     * @return  string value
     * @throws  lang.IllegalArgumentException in case not mapping can be found
     */
    public function getMapping($key) {
      if (!isset($this->mapping[$key])) {
        throw new IllegalArgumentException('Mapping for "'.$key.'" not found');
      }
      
      return $this->mapping[$key];
    }
    
    /**
     * Set current class
     *
     * @param   text.doclet.ClassDoc c
     */
    public function setCurrentClass($c) {
      $this->current= $c;
    }
    
    /**
     * Retrieves qualified name of a given short name
     *
     * @param   string
     * @return  string
     * @throws  lang.IllegalArgumentException in case not mapping can be found
     */
    public function qualifiedNameOf($short) {
      $mapped= $this->getMapping($short);      
      return ($this->current && $this->current->qualifiedName() == $mapped ? 'self' : $mapped);
    }

    /**
     * Retrieves packaged name of a given qualified name
     *
     * @param   string q qualified class name
     * @return  string
     */
    public function packagedNameOf($q) {
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
     * @param   string type
     * @param   bool arg default FALSE
     * @return  string
     */
    public function forType($type, $arg= FALSE) {
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
