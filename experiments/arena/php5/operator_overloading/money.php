<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ class Money 
  class Money {
    protected static
      $conversion = array(
        'USD,EUR'   => 0.8286,
        'EUR,USD'   => 1.2068
      );
        
    protected
      $amount   = 0.0,
      $currency = '';
    
    public function __construct($amount, $currency) {
      $this->amount= $amount;
      $this->currency= $currency;
    }
    
    protected function amountIn($targetCurrency) {
      if ($this->currency == $targetCurrency) {     // Border case
        return $this->amount;
      }
      return $this->amount * self::$conversion[$this->currency.','.$targetCurrency];
    }
    
    public function convertTo($targetCurrency) {
      return new Money($this->amountIn($targetCurrency), $targetCurrency);
    }
    
    public static operator + (Money $m1, Money $m2) {
      return new Money($m1->amount + $m2->amountIn($m1->currency), $m1->currency);
    }

    public static operator - (Money $m1, Money $m2) {
      return new Money($m1->amount - $m2->amountIn($m1->currency), $m1->currency);
    }
    
    public static operator __compare (Money $m1, Money $m2) {
      return strcmp(
        sprintf('%.4f', $m1->amountIn($m2->currency)),
        sprintf('%.4f', $m2->amount)
      );
    }

    public function toString() {
      return sprintf('%.4f %s', $this->amount, $this->currency);
    }
  }
  // }}}
  
  // {{{ main
  Reflection::export(new ReflectionClass('Money'));
  
  $eur= new Money(1.0, 'EUR');
  $usd= new Money(1.0, 'USD');
  
  // Convert
  echo $eur->toString(), ' in USD = ', $eur->convertTo('USD')->toString(), "\n";
  echo $usd->toString(), ' in EUR = ', $usd->convertTo('EUR')->toString(), "\n";
  
  // Sum
  $sum= $eur + $usd;
  echo $eur->toString(), ' + ', $usd->toString(), ' = ', $sum->toString(), "\n"; 
  
  // Compare
  echo $eur->toString(), ' == ', $usd->toString(), ' ? ', var_export($eur == $usd, 1), "\n";
  echo $eur->toString(), ' <= ', $usd->toString(), ' ? ', var_export($eur <= $usd, 1), "\n";
  echo $eur->toString(), ' >= ', $usd->toString(), ' ? ', var_export($eur >= $usd, 1), "\n";
  // }}}
?>
