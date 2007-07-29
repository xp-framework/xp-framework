<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.cmd.Command',
    'strategies.PublicCallStrategy',
    'strategies.PrivateCallStrategy',
    'strategies.ProtectedCallStrategy',
    'util.profiling.Timer'
  );

  /**
   * Test method calls
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Profiler extends Command {
    protected
      $strategy = NULL,
      $times    = 0;

    /**
     * Set strategy to use (one of public, private, protected)
     *
     * @param   string name 
     */
    #[@arg(position= 0)]
    public function setStrategy($name) {
      $this->strategy= XPClass::forName('strategies.'.ucfirst($name).'CallStrategy')->newInstance();
    }

    /**
     * Set how many times to run
     *
     * @param   int times default 100000
     */
    #[@arg]
    public function setTimes($times= 100000) {
      $this->times= $times;
    }
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $t= new Timer();
      with ($t->start()); {
        $this->strategy->run($this->times);
        $t->stop();

        $this->out->writeLinef(
          '%s: %.3f seconds for %d calls', 
          $this->strategy->getClassName(), 
          $t->elapsedTime(), 
          $this->times
        );
      }
    }
  }
?>
