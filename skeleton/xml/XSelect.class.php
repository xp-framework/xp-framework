<?php
  define('E_XSELECT_ELEMENT_NOTFOUND_EXCEPTION',   0xFF01);
  
  class XSelect extends XML {
    var $tree;
    
    var $_cache;
    
    function XSelect(&$tree) {
      Object::__construct();
      $this->tree= &$tree;
    }
    
    function _recurse(&$children, $inset= '') {
      foreach ($children as $idx => $child) {
        $member= $inset.$child->name;
        $this->_cache[$member][]= &$children[$idx];
        
        if (isset($child->children)) {
          $this->_recurse(&$children[$idx]->children, $member.'/', &$args);
        }
      }
    }
    
    function _buildCache() {
      if (!isset($this->_cache)) {
        $this->_recurse(&$this->tree->children);
      }
    }
    
    function reset() {
      unset($this->_cache);
    }
    
    function &nodeset($path) {
      $this->_buildCache();
      return $this->_cache[$path];
    }
    
    function &node($path, $idx) {
      $this->_buildCache();
      return $this->_cache[$path][$idx];
    }
    
    function content($path, $idx= 0) {
      $this->_buildCache();
      if (!isset($this->_cache[$path][$idx]->content)) {
        throw(E_XSELECT_ELEMENT_NOTFOUND_EXCEPTION, $path);
        return NULL;
      }
      return $this->_cache[$path][$idx]->content;
    }

    function __destruct() {
      parent::__destruct();
    }
  }
?>
