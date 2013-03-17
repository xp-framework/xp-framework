<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The boolean wrapper class
   *
   * @test    xp://net.xp_framework.unittest.core.types.BooleanTest
   */
  class Boolean extends Object {
    public static $TRUE, $FALSE;
    public $value = FALSE;

    static function __static() {
      self::$TRUE= new self(TRUE);
      self::$FALSE= new self(FALSE);
    }

    /**
     * Constructor. Accepts one of the following:
     *
     * <ul>
     *   <li>The values TRUE or FALSE</li>
     *   <li>An integer - any non-zero value will be regarded TRUE</li>
     *   <li>The strings "true" and "false", case-insensitive</li>
     *   <li>Numeric strings - any non-zero value will be regarded TRUE</li>
     * </ul>
     *
     * @param   var value
     * @throws  lang.IllegalArgumentException if value is not acceptable
     */
    public function __construct($value) {
      if (TRUE === $value || FALSE === $value) {
        $this->value= $value;
      } else if (is_int($value)) {
        $this->value= 0 !== $value;
      } else if ('0' === $value) {
        $this->value= FALSE;
      } else if (is_string($value) && ($l= strlen($value)) && strspn($value, '1234567890') === $l) {
        $this->value= TRUE;
      } else if (0 === strncasecmp($value, 'true', 4)) {
        $this->value= TRUE;
      } else if (0 === strncasecmp($value, 'false', 5)) {
        $this->value= FALSE;
      } else {
        throw new IllegalArgumentException('Not a valid boolean: '.xp::stringOf($value));
      }
    }

    /**
     * ValueOf factory
     *
     * @param   string $value
     * @return  self
     */
    public static function valueOf($value) {
      return new self($value);
    }

    /**
     * Returns the value of this number as an int.
     *
     * @return  int
     */
    public function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns a hashcode for this number
     *
     * @return  string
     */
    public function hashCode() {
      return $this->value ? 'true' : 'false';
    }

    /**
     * Returns a string representation of this number object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.($this->value ? 'true' : 'false').')';
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @param   lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->value === $cmp->value;
    }
  }
?>
