<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
 
  uses('util.log.Logger', 'util.log.FileAppender');
 
  class GtkApplication extends Object {
    var 
      $name,
      $window,
      $rcFile;
    
    var 
      $_log= NULL,
      $_done= FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name Name der Anwendung
     */
    function __construct($name) {
      $this->name= $name;
      $l= &Logger::getInstance();
      $this->_log= $l->getCategory($this->name);
      $this->_log->identifier= $name;
      $this->_log->addAppender(new FileAppender('php://stderr'));
      parent::__construct();
    }

    /**
     * Erzeugt das Hauptfenster
     *
     * @access  private
     */
    function _create() {
      if (!isset($this->window)) $this->window= &new GtkWindow();
    }

    /**
     * Initialisiert die Anwendung
     *
     * @access  public
     */
    function init() {
      $this->log('init');
      if (isset($this->rcFile)) Gtk::rc_parse($this->rcFile); 
      $this->_create();
      $this->window->connect('destroy', array(&$this, 'done'));
      $this->window->show_all();
    }

    /**
     * Logmeldung auf die Konsole
     *
     * @access  public
     * @param   mixed vars Variablen
     */    
    function log() {
      $variables= func_get_args();
      call_user_func_array(array($this->_log, 'info'), $variables);
    }

    /**
     * Anwendung ausführen
     *
     * @access  public
     */    
    function run() {
      $this->log('main');
      Gtk::main();
    }

    /**
     * Anwendung beenden
     *
     * @access  public
     */       
    function done() {
      if ($this->_done) return;
      
      $this->log('done');
      $this->_done= TRUE;
      Gtk::main_quit();
    }
  }
?>

