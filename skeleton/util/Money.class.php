<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Currency', 'lang.types.Double');

  /**
   * Represents money.
   *
   * <code>
   *   $ten= new Money(10, Currency::$USD);
   *   
   *   // Adding money
   *   $eleven= $ten->add(new Money(1, Currency::$USD));
   *   
   *   // Multiplying it
   *   $twentytwo= $eleven->multiplyBy(2);
   *   
   *   // Dividing it
   *   $two= $eleven->divideBy(11);
   *   
   *   // Subtracting it
   *   $one= $eleven->subtract(new Money(1, Currency::$USD));
   * </code>
   *
   * @ext     bc
   * @test    xp://net.xp_framework.unittest.util.MoneyTest
   * @see     xp://util.Currency
   * @see     http://martinfowler.com/eaaCatalog/money.html
   */
  class Money extends Object {
    protected $amount   = '';
    protected $currency = NULL;

    static function __static() {
      bcscale(ini_get('precision'));
    }

    /**
     * Constructor
     *
     * @param   var amount
     * @param   util.Currency currency
     */
    public function __construct($amount, Currency $currency) {
      $this->amount= (string)$amount;
      $this->currency= $currency;
    }

    /**
     * Get amount as double
     *
     * @return  lang.types.Double
     */
    public function amount() {
      return new Double((double)$this->amount);
    }

    /**
     * Get currency
     *
     * @return  util.Currency
     */
    public function currency() {
      return $this->currency;
    }

    
    /**
     * Add another money value to this money and return a new money 
     * instance.
     *
     * @param   util.Money m
     * @return  util.Money
     * @throws  lang.IllegalArgumentException if the currencies don't match
     */
    public function add(Money $m) {
      if (!$this->currency->equals($m->currency)) {
        throw new IllegalArgumentException('Cannot add '.$m->currency->name().' to '.$this->currency->name());
      }
      return new self(bcadd($this->amount, $m->amount), $this->currency);
    }

    /**
     * Subtract another money value from this money and return a new money 
     * instance.
     *
     * @param   util.Money m
     * @return  util.Money
     * @throws  lang.IllegalArgumentException if the currencies don't match
     */
    public function subtract(Money $m) {
      if (!$this->currency->equals($m->currency)) {
        throw new IllegalArgumentException('Cannot subtract '.$m->currency->name().' from '.$this->currency->name());
      }
      return new self(bcsub($this->amount, $m->amount), $this->currency);
    }

    /**
     * Multiply this money value with the given amount.
     *
     * @param   var multiplicand
     * @return  util.Money
     */
    public function multiplyBy($multiplicand) {
      return new self(bcmul($this->amount, (string)$multiplicand), $this->currency);
    }

    /**
     * Divide this money value by the given amount.
     *
     * @param   var divisor
     * @return  util.Money
     */
    public function divideBy($divisor) {
      return new self(bcdiv($this->amount, (string)$divisor), $this->currency);
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return number_format($this->amount, 2).' '.$this->currency->name();
    }

    /**
     * Compare this amount of value to another
     *
     * @param   util.Money m
     * @return  int equal: 0, m less than this: -1, 1 otherwise
     * @throws  lang.IllegalArgumentException if the currencies don't match
     */
    public function compareTo(Money $m) {
      if (!$this->currency->equals($m->currency)) {
        throw new IllegalArgumentException('Cannot compare '.$m->currency->name().' to '.$this->currency->name());
      }
      return bccomp($m->amount, $this->amount);
    }
    
    /**
     * Returns whether a given object is equal to this money instance
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        0 === bccomp($this->amount, $cmp->amount) && 
        $this->currency->equals($cmp->currency)
      );
    }
  }
?>
