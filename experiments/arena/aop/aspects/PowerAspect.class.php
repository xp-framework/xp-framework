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
    #[@around('methodInvoked')]
    public function logCall($j, $args) {
      Console::writeLine('Intercepted method call to ', $j[0]->getClassName(), '::', substr($j[1], 1), '(', implode(', ', array_map(array('xp', 'stringOf'), $args)), ')');
      $s= microtime(TRUE);
      try {
        $r= call_user_func_array($j, $args);
      } catch (Throwable $e) {
        Console::writeLine('  ** ', $e->compoundMessage(), ', took ', sprintf('%.3f', (microtime(TRUE)- $s) * 1000), ' milliseconds');
        throw $e;
      }
      Console::writeLine('  => ', $r, ', took ', sprintf('%.3f', (microtime(TRUE)- $s) * 1000), ' milliseconds');
      return $r;
    }
  }
?>
