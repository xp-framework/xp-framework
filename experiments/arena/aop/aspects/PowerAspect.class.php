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
     * Aditionally, intercept calls to any of the Binford class' methods
     *
     */
    #[@pointcut('util.Binford::*')]
    public function methodInvoked() { }

    /**
     * Check power
     *
     * @param   int p
     */
    #[@before('settingPower')]
    public function checkPower($j, $args) {
      if ($args[0] != 6100 && $args[0] != 611) { 
        throw new IllegalArgumentException('Power must either be 611 or 6100'); 
      }
    }

    /**
     * Log power was set
     *
     */
    #[@after('settingPower')]
    public function logPower($j) {
      Console::writeLine('Power successfully set!');
    }

    /**
     * Log power was set
     *
     */
    #[@before('methodInvoked')]
    public function logCall($j) {
      Console::writeLine('Method ', $j, ' invoked');
    }
  }
?>
