<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.IndexOutOfBoundsException');

  /**
   * Represents a match result
   *
   * @see      xp://text.regex.Pattern#matches
   * @purpose  Result
   */
  class MatchResult extends Object {
    protected $length  = 0;
    protected $matches = array();
    
    public static $EMPTY;
    
    static function __static() {
      self::$EMPTY= new self(0, array());
    }
    
    /**
     * Constructor
     *
     * @param   int length
     * @param   string[][] matches
     */
    public function __construct($length, $matches) {
      $this->length= $length;
      $this->matches= $matches;
    }
    
    /**
     * Return how many matches there where
     *
     * @return  int
     */
    public function length() {
      return $this->length;
    }

    /**
     * Returns all matched groups
     *
     * @return  string[][]
     */
    public function groups() {
      return $this->matches;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->length.') '.($this->matches ? xp::stringOf($this->matches) : '<EMPTY>');
    }

    /**
     * Returns the matched group with the specified group offset
     *
     * @param   int offset
     * @return  string[]
     * @throws  lang.IndexOutOfBoundsException in case a group with the given offset does not exist
     */
    public function group($offset) {
      if (!isset($this->matches[$offset])) {
        throw new IndexOutOfBoundsException('No such group '.$offset);
      }
      return $this->matches[$offset];
    }
    
    /**
     * Returns whether an object is equal to this match result.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $cmp->length === $this->length && 
        $cmp->matches === $this->matches
      );
    }
  }
?>
