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
     * Additionally, intercept calls to any of the Binford class' methods
     *
     */
    #[@pointcut('util.Binford::*')]
    public function methodInvocation() { }

    /**
     * Check power
     *
     * @param   int p
     */
    #[@before('settingPower')]
    public function checkPower(JoinPoint $jp) {
      if ($jp->args[0] != 6100 && $jp->args[0] != 611) { 
        throw new IllegalArgumentException('Power must either be 611 or 6100'); 
      }
    }

    /**
     * Log power was set
     *
     */
    #[@after('settingPower')]
    public function logPower(JoinPoint $jp, $return) {
      Console::writeLine('Power successfully set!');
    }

    /**
     * Log power was set
     *
     */
    #[@around('methodInvocation')]
    public function logCall(JoinPoint $jp) {
      Console::writeLine('Intercepted method call to ', $jp);
      $s= microtime(TRUE);
      try {
        $r= $jp->proceed();
      } catch (Throwable $e) {
        Console::writeLine('  ** ', $e->compoundMessage(), ', took ', sprintf('%.3f', (microtime(TRUE)- $s) * 1000), ' milliseconds');
        throw $e;
      }
      Console::writeLine('  => ', $r, ', took ', sprintf('%.3f', (microtime(TRUE)- $s) * 1000), ' milliseconds');
      return $r;
    }
  }
?>
