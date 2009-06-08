<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.doclet.AnnotatedDoc');

  /**
   *
   * @purpose  Documents a method
   */
  class MethodDoc extends AnnotatedDoc {
    public
      $arguments    = array(),
      $modifiers    = array(),
      $declaring    = NULL;

    /**
     * Returns method access level (one of public, private or protected)
     *
     * @return  string
     */
    public function getAccess() {
      foreach (array('public', 'private', 'protected') as $m) {
        if (isset($this->modifiers[$m])) return $m;
      }
      return 'public';  // Implicit public
    }
    
    /**
     * Returns modifiers as a hashmap (modifier names as keys for easy
     * O(1) lookup).
     *
     * @return  array<string, TRUE> 
     */
    public function getModifiers() {
      return $this->modifiers;
    }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->declaring->qualifiedName().'::'.$this->name.'>';
    }
  }
?>
