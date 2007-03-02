<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.invoke.InvocationChain');

  /**
   * Intercepts invocations
   *
   * @test     xp://tests.InvocationChainTest
   * @purpose  Interface
   */
  interface InvocationInterceptor {

    /**
     * Invokation handler
     *
     * @param   de.schlund.intranet.search.interceptor.InvocationChain chain
     * @throws  lang.Throwable to indicate failure
     * @return  lang.Objkect
     */
    public function invoke(InvocationChain $chain);
  
  }
?>
