<?php
/* This file provides the GTK sapi for the XP framework
 * 
 * $Id$
 */

  uses('util.cmd.ParamString', 'util.cmd.Console');
  
  define('EPREPEND_IDENTIFIER', "\6100");
  
  // {{{ final class sapi·gtk
  class sapi·gtk {

    // {{{ internal string output(string buf)
    //     Output handler
    static function output($buf) {

      // Check for fatal errors
      if (FALSE !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
        $e= new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p)));
        return '[sapi::gtk] Uncaught error: '.$e->toString();
      }

      return $buf;
    }
    // }}}

    // {{{ internal void except(Exception e)
    //     Exception handler
    static function except($e) {
      echo '[sapi::gtk] Uncaught exception: '.xp::stringOf($e);
    }    
    // }}}
  }
  // }}}
  
  // {{{ void run (&org.gnome.GtkApplication app)
  //     Runs a GTK app
  function run($app) {
    try {
      $app->init();
    } catch (GuiException $e) {
      xp::error('Error initializing '.$app->getClassName().': '.$e->toString());
      // Bails out
    }
    $app->run();
    $app->done();
  }
  // }}}
  
  if (!extension_loaded('php-gtk')) {
    xp::error('[sapi::gtk] GTK extension not available');
    // Bails out
  }

  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);
  set_exception_handler(array('sapi·gtk', 'except'));
  ob_start(array('sapi·gtk', 'output'));
?>
