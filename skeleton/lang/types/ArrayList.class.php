<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a "numeric" array
   *
   * @purpose  Wrapper
   */
  class ArrayList extends Object {
    public
      $values=  NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed[] values default array()
     */
    public function __construct($values= array()) {
      $this->values= $values;
    }
    
    /**
     * Helper method to compare two arrays recursively
     *
     * @access  protected
     * @param   array a1
     * @param   array a2
     * @return  bool
     */
    public function arrayequals($a1, $a2) {
      if (sizeof($a1) != sizeof($a2)) return FALSE;

      foreach (array_keys($a1) as $k) {
        switch (TRUE) {
          case !array_key_exists($k, $a2): 
            return FALSE;

          case is_array($a1[$k]):
            if (!$this->arrayequals($a1[$k], $a2[$k])) return FALSE;
            break;

          case is('Generic', $a1[$k]):
            if (!$a1[$k]->equals($a2[$k])) return FALSE;
            break;

          case $a1[$k] !== $a2[$k]:
            return FALSE;
        }
      }
      return TRUE;
    }
    
    /**
     * Checks whether a given object is equal to this arraylist
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals(&$cmp) {
      return is('ArrayList', $cmp) && $this->arrayequals($this->values, $cmp->values);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return (
        $this->getClassName().'['.sizeof($this->values)."]@{".
        implode(', ', array_map(array('xp', 'stringOf'), $this->values)).
        '}'
      );
    }
  }
?>
