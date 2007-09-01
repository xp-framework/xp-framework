<?php
/* This class is part of the XP framework
 *
 * $Id: FieldDoc.class.php 9051 2006-12-29 17:17:14Z friebe $
 */

  namespace text::doclet;
 
  uses('text.doclet.Doc');

  /**
   *
   * @purpose  Documents a class field
   */
  class FieldDoc extends Doc {
    public
      $modifiers     = array(),
      $constantValue = NULL;

    /**
     * Retrieve initial value
     *
     * @return  mixed
     */
    public function constantValue() {
      return $this->constantValue;
    }
    
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
