<?php
/* This file provides the class sapi for the XP framework
 * 
 * $Id$
 */
  uses('util.cmd.ParamString');
  
  // {{{ final class sapi·class·classrunner
  class sapi·class·classrunner {
    var
      $paramstring      = NULL,
      $invokationtarget = '';
    
    // {{{ void sapi·class·classrunner(void)
    //     Runs the method main() of the script's class        
    function sapi·class·classrunner() {
      $this->paramstring= &new ParamString();
      $this->invokationtarget= xp::reflect(basename($this->paramstring->value(0), '.class.php'));
      xp::registry('class.'.$this->invokationtarget, $this->invokationtarget);
    }
    // }}}

    // {{{ classrunner instance(void)
    //     Returns the instance of this class
    function instance() {
      static $instance= NULL;
      
      if (!$instance) $instance= &new sapi·class·classrunner();
      return $instance;
    }    
    // }}}

    // {{{ &lang.Object instanciate(void)
    //     Instanciate the class
    function &instanciate() {
      return new $this->invokationtarget();
    }
    // }}}
    
    // {{{ int run(void)
    //     Runs the method main() of the script's class
    function run() {
      $itarget= array($this->invokationtarget, 'main');
      if (!is_callable($itarget)) {
        xp::error('Target '.implode('::', $itarget).' is not runnable');
        // Bails out
      }
      return call_user_func($itarget, $this->paramstring);
    }
    // }}}

  }
  // }}}
  
  // {{{ final class this
  class this {
  
    // {{{ &lang.Object newInstance(void)
    //     Instanciate a new object
    function &newInstance() {
      with ($i= &sapi·class·classrunner::instance()); {
        return $i->instanciate();
      }
    }
    // }}}

  }
  // }}}
  
  // {{{ void run(void)
  //     Wrapper for classrunner::run
  function run() {
    with ($i= &sapi·class·classrunner::instance()); {
      exit($i->run());
    }
  }
  
  // }}}
?>
