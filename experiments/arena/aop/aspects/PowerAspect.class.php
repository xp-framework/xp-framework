<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Power aspect
   *
   * @purpose  AOP
   */
  #[@aspect]
  class PowerAspect extends Object {
  
    static function __static() {
      Aspects::register(new self());
    }
  
    /**
     * Intercept calls to Binford class' setPoweredBy() method
     *
     */
    #[@pointcut('util.Binford::setPoweredBy')]
    public function settingPower() { }
  
    /**
     * Check power
     *
     * @param   int p
     */
    #[@before('settingPower')]
    public function checkPower($p) {
      if ($p != 6100 && $p != 611) { 
        throw new IllegalArgumentException('Power must either be 611 or 6100'); 
      }
    }

    /**
     * Log power was set
     *
     */
    #[@after('settingPower')]
    public function logPower() {
      Console::writeLine('Power successfully set!');
    }
  }
?>
