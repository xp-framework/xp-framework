<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
 require('lang.base.php');
 uses('Signal');
 
 declare(ticks=1);
 
 // {{{ class MySignalHandler
 class MySignalHandler {
   function handle($sig) {
     echo 'Caught ', $sig, "\n";
     exit();
   }
 } implements('MySignalHandler.class.php', 'SignalHandler');
 // }}}
 
 // {{{ main
 Signal::handle(new Signal(SIGINT), new MySignalHandler());
 for ($i= 0; $i < 100; $i++) {
   echo '.'; flush(); sleep(1);
 }
 // }}}
?>
