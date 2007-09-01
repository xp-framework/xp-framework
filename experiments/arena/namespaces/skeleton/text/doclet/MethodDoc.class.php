<?php
/* This class is part of the XP framework
 *
 * $Id: MethodDoc.class.php 9018 2006-12-29 12:52:58Z friebe $
 */

  namespace text::doclet;
 
  uses('text.doclet.AnnotatedDoc');

  /**
   *
   * @purpose  Documents a method
   */
  class MethodDoc extends AnnotatedDoc {
    public
      $arguments    = array(),
      $modifiers    = array();

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
  }
?>
