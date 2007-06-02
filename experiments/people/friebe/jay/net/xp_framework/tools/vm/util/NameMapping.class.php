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
      $this->mapping[strtolower($key)]= $value;
    }

    /**
     * Retrieve a mapping
     *
     * @param   string key
     * @return  string value
     * @throws  lang.IllegalArgumentException in case not mapping can be found
     */
    public function getMapping($key) {
      $lookup= strtolower($key);
      if (!isset($this->mapping[$lookup])) {
        throw new IllegalArgumentException('Mapping for "'.$key.'" not found');
      }
      
      return $this->mapping[$lookup];
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
      if (strstr($short, '.')) {
        $q= $short;
      } else {
        $q= $this->getMapping($short);
      }
      if (!$this->current) return $q;

      $current= $this->current->qualifiedName();
      if ($current === $q) {

        // If this class is the same as the current, use "self" keyword        
        return 'self';
      } else if (substr($current, 0, strrpos($current, '.')) == substr($q, 0, strrpos($q, '.'))) {

        // If this class is in the same package as the current omit package name
        return substr($q, strrpos($q, '.')+ 1);
      }
      
      return $q;
    }

    /**
     * Retrieves packaged name of a given qualified name
     *
     * @param   string q qualified class name
     * @return  string
     */
    public function packagedNameOf($q) {
      return strtr($q, '.', $this->namespaceSeparator);
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
        'int'      => TRUE,
        'float'    => TRUE,
        'bool'     => TRUE,
        'string'   => TRUE,
        'array'    => TRUE,
        'mixed'    => TRUE,
        'resource' => TRUE
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
        $type= $this->packagedNameOf($this->qualifiedNameOf($type));
      }
      return $type.$va.$array;
    }
  }
?>
