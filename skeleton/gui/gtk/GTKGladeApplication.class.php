<?php  
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  uses('gui.gtk.GTKApplication');
  
  class GTKGladeApplication extends GTKApplication {
    var $glade;
    var $gladeFile;
    var $windowWidget;

    function __construct($name, $gladeFile, $windowWidget= 'window1') {
      parent::__construct($name);
      $this->gladeFile= $gladeFile;
      $this->windowWidget= $windowWidget;
    }

    function _create() {
      if (!isset($this->window)) $this->window= &$this->glade->get_widget($this->windowWidget);
    }

    function init() {
      $this->log('init', $this->gladeFile);
      if (isset($this->rcFile)) Gtk::rc_parse($this->rcFile); 
      $this->glade= &new GladeXML($this->gladeFile);
      parent::init();
    }
    
    function &widget($name) {
      return $this->glade->get_widget($name);
    }
  }
?>

