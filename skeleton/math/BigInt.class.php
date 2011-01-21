<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('math.BigNum', 'math.BigFloat');

  /**
   * A big integer
   *
   * @see     xp://math.BigNum
   * @test    xp://net.xp_framework.unittest.math.BigIntTest
   * @test    xp://net.xp_framework.unittest.math.BigIntAndFloatTest
   */
  class BigInt extends BigNum {

    /**
     * Creates a new BigInt instance
     *
     * @param   string in
     */
    public function __construct($in) {
      $this->num= substr($in, 0, strcspn($in, '.'));
    }

    /**
     * +
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function add($other) {
      if ($other instanceof self) {
        return new self(bcadd($this->num, $other->num));
      } else if (is_int($other)) {
        return new self(bcadd($this->num, $other));
      } else if ($other instanceof BigFloat) {
        return new BigFloat(bcadd($this->num, $other->num));
      } else {
        return new BigFloat(bcadd($this->num, $other));
      }
    }

    /**
     * -
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function subtract($other) {
      if ($other instanceof self) {
        return new self(bcsub($this->num, $other->num));
      } else if (is_int($other)) {
        return new self(bcsub($this->num, $other));
      } else if ($other instanceof BigFloat) {
        return new BigFloat(bcsub($this->num, $other->num));
      } else {
        return new BigFloat(bcsub($this->num, $other));
      }
    }

    /**
     * *
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function multiply($other) {
      if ($other instanceof self) {
        return new self(bcmul($this->num, $other->num));
      } else if (is_int($other)) {
        return new self(bcmul($this->num, $other));
      } else if ($other instanceof BigFloat) {
        return new BigFloat(bcmul($this->num, $other->num));
      } else {
        return new BigFloat(bcmul($this->num, $other));
      }
    }

    /**
     * /
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function divide($other) {
      if ($other instanceof self) {
        if (NULL === ($r= bcdiv($this->num, $other->num, 0))) {     // inlined
          $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
          xp::gc(__FILE__);
          throw new IllegalArgumentException($e);
        }
        return new self($r);
      } else if (is_int($other)) {
        if (NULL === ($r= bcdiv($this->num, $other, 0))) {          // inlined
          $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
          xp::gc(__FILE__);
          throw new IllegalArgumentException($e);
        }
        return new self($r);
      } else if ($other instanceof BigFloat) {
        if (NULL === ($r= bcdiv($this->num, $other->num))) {        // inlined
          $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
          xp::gc(__FILE__);
          throw new IllegalArgumentException($e);
        }
        return new BigFloat($r);
      } else {
        if (NULL === ($r= bcdiv($this->num, $other))) {             // inlined
          $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
          xp::gc(__FILE__);
          throw new IllegalArgumentException($e);
        }
        return new BigFloat($r);
      }
    }

    /**
     * +(0), strictly integer addition
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function add0($other) {
      return new self(bcadd($this->num, $other instanceof parent ? $other->num : $other, 0));
    }

    /**
     * -(0), strictly integer subtraction
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function subtract0($other) {
      return new self(bcsub($this->num, $other instanceof parent ? $other->num : $other, 0));
    }

    /**
     * *(0), strictly integer multiplication
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function multiply0($other) {
      return new self(bcmul($this->num, $other instanceof self ? $other->num : $other, 0));
    }

    /**
     * /
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function divide0($other) {
      if (NULL === ($r= bcdiv($this->num, $other instanceof self ? $other->num : $other, 0))) {
        $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
        xp::gc(__FILE__);
        throw new IllegalArgumentException($e);
      }
      return new self($r);
    }

    /**
     * ^
     *
     * @see     http://en.wikipedia.org/wiki/Exponentiation
     * @param   var other
     * @return  math.BigNum
     */
    public function power($other) {
      if ($other instanceof self) {
        return new self(bcpow($this->num, $other->num));
      } else if (is_int($other)) {
        return new self(bcpow($this->num, $other));
      } else if ($other instanceof BigFloat) {
        if (strpos($other->num, '.')) {             // inlined
          throw new IllegalArgumentException('Decimal exponents not supported');
        }
        return new BigFloat(bcpow($this->num, $other->num));
      } else {
        if (strpos($other, '.')) {                 // inlined
          throw new IllegalArgumentException('Decimal exponents not supported');
        }
        return new BigFloat(bcpow($this->num, $other));
      }
    }

    /**
     * %
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function modulo($other) {
      if (NULL === ($r= bcmod($this->num, $other instanceof self ? $other->num : $other))) {
        $e= key(xp::$registry['errors'][__FILE__][__LINE__- 1]);
        xp::gc(__FILE__);
        throw new IllegalArgumentException($e);
      }
      return new $this($r);
    }
    
    /**
     * &
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function bitwiseAnd($other) {
      $a= self::bytesOf($this->num);
      $b= self::bytesOf($other instanceof self ? $other->num : $other);
      $l= max(strlen($a), strlen($b));
      return self::fromBytes(str_pad($a, $l, "\0", STR_PAD_LEFT) & str_pad($b, $l, "\0", STR_PAD_LEFT));
    }

    /**
     * |
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function bitwiseOr($other) {
      $a= self::bytesOf($this->num);
      $b= self::bytesOf($other instanceof self ? $other->num : $other);
      $l= max(strlen($a), strlen($b));
      return self::fromBytes(str_pad($a, $l, "\0", STR_PAD_LEFT) | str_pad($b, $l, "\0", STR_PAD_LEFT));
    }

    /**
     * ^
     *
     * @param   var other
     * @return  math.BigNum
     */
    public function bitwiseXor($other) {
      $a= self::bytesOf($this->num);
      $b= self::bytesOf($other instanceof self ? $other->num : $other);
      $l= max(strlen($a), strlen($b));
      return self::fromBytes(str_pad($a, $l, "\0", STR_PAD_LEFT) ^ str_pad($b, $l, "\0", STR_PAD_LEFT));
    }

    /**
     * >>
     *
     * @param   var shift
     * @return  math.BigNum
     */
    public function shiftRight($shift) {
      return new self(bcdiv($this->num, bcpow(2, $shift instanceof self ? $shift->num : $shift), 0));
    }
    
    /**
     * <<
     *
     * @param   var shift
     * @return  math.BigNum
     */
    public function shiftLeft($shift) {
      return new self(bcmul($this->num, bcpow(2, $shift instanceof self ? $shift->num : $shift), 0));
    }
    
    /**
     * Creates a bignum from a sequence of bytes
     *
     * @see     xp://math.BigNum#toBytes
     * @param   string bytes
     * @return  math.BigNum
     */
    protected static function fromBytes($bytes) {
      $len= strlen($bytes);
      $len+= (3 * $len) % 4;
      $bytes= str_pad($bytes, $len, "\0", STR_PAD_LEFT);
      $self= new self(0);
      for ($i= 0; $i < $len; $i+= 4) {
        $self->num= bcadd(bcmul($self->num, '4294967296'), 0x1000000 * ord($bytes{$i}) + current(unpack('N', "\0".substr($bytes, $i+ 1, 3))));
      }      
      return $self;
    }
    
    /**
     * Creates sequence of bytes from a bignum
     *
     * @see     xp://math.BigNum#fromBytes
     * @return  string
     */
    protected static function bytesOf($n) {
      $value= '';
      while (bccomp($n, 0) > 0) {
        $value= substr(pack('N', bcmod($n, 0x1000000)), 1).$value;
        $n= bcdiv($n, 0x1000000);
      }
      return ltrim($value, "\0");    
    }
    
    /**
     * String cast overloading
     *
     * @return  string
     */
    public function __toString() {
      return $this->num;
    }

    /**
     * Returns an byte representing this big integer
     *
     * @return  int
     */
    public function byteValue() {
      return $this->bitwiseAnd(0xFF)->intValue();
    }
  }
?>
