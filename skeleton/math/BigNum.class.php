<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A big number
   *
   * @see      php://pack
   * @see      php://unpack
   * @see      http://pear.php.net/package/Math_BigInteger/docs/latest/__filesource/fsource_Math_BigInteger__Math_BigInteger-1.0.0RC3BigInteger.php.html#a1669
   * @see      http://sine.codeplex.com/SourceControl/changeset/view/57274#1535069 
   * @ext      bcmath
   */
  abstract class BigNum extends Object {
  
    static function __static() {
      bcscale(ini_get('precision'));
    }
    
    /**
     * +
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function add($other) {
      return new $this(bcadd($this->num, $other instanceof self ? $other->num : $other));
    }

    /**
     * -
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function subtract($other) {
      return new $this(bcsub($this->num, $other instanceof self ? $other->num : $other));
    }

    /**
     * *
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function multiply($other) {
      return new $this(bcmul($this->num, $other instanceof self ? $other->num : $other));
    }

    /**
     * /
     *
     * @param   var other
     * @return  math.BigNum
     */
    public abstract function divide($other);

    /**
     * Returns whether another object is equal to this
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof $this && 0 === bccomp($cmp->num, $this->num);
    }
    
    /**
     * Returns an integer representing this bignum
     *
     * @return  int
     */
    public function intValue() {
      return (int)substr($this->num, 0, strcspn($this->num, '.'));
    }

    /**
     * Returns a double representing this bignum
     *
     * @return  int
     */
    public function doubleValue() {
      return (double)$this->num;
    }
  }
?>
