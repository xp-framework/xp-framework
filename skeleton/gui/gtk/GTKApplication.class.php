<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  class GtkApplication extends Object {
    var $name;
    var $window;
    var $rcFile;

    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }

    function _create() {
      if (!isset($this->window)) $this->window= &new GtkWindow();
    }

    function init() {
      $this->log('init');
      if (isset($this->rcFile)) Gtk::rc_parse($this->rcFile); 
      $this->_create();
      $this->window->connect('destroy', array(&$this, 'done'));
      $this->window->show_all();
    }
    
    function log($msg, $var= NULL) {
      echo $msg.' :: ';
      var_dump($var);
    }
    
    function run() {
      $this->log('main');
      Gtk::main();
    }
    
    function done() {
      $this->log('done');
      Gtk::main_quit();
    }
  }
?>

