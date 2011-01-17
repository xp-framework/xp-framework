<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('math.BigNum');

  /**
   * A big float
   *
   * @see   xp://math.BigNum
   */
  class BigFloat extends BigNum {

    /**
     * Creates a new bigfloat instance
     *
     * @param   string in
     */
    public function __construct($in) {
      parent::__construct(FALSE !== strpos($in, '.') ? rtrim(rtrim($in, '0'), '.') : $in);
    }
  
    /**
     * Returns the next lowest "integer" value by rounding down value if necessary. 
     *
     * @return  math.BigFloat
     */
    public function ceil() {
      return new self(FALSE === strpos($this->num, '.') 
        ? $this->num 
        : ('-' === $this->num{0} ? bcsub($this->num, 0, 0) : bcadd($this->num, 1, 0))
      );
    }

    /**
     * Returns the next highest "integer" value by rounding up value if necessary
     *
     * @return  math.BigFloat
     */
    public function floor() {
      return new self(FALSE === strpos($this->num, '.') 
        ? $this->num 
        : ('-' === $this->num{0} ? bcsub($this->num, 1, 0) : bcadd($this->num, 0, 0))
      );
    }

    /**
     * Returns the rounded value of val to specified precision (number of digits 
     * after the decimal point).
     *
     * @param   int precision
     * @return  math.BigFloat
     */
    public function round($precision= 0) {
      if (FALSE === strpos($this->num, '.')) return new self($this->num);
      
      $a= '0.'.str_repeat('0', $precision).'5';
      return new self(FALSE === strpos($this->num, '.') 
        ? $this->num 
        : ('-' === $this->num{0} ? bcsub($this->num, $a, $precision) : bcadd($this->num, $a, $precision))
      );
    }

    /**
     * String cast overloading
     *
     * @return  string
     */
    public function __toString() {
      return $this->num;
    }
  }
?>
