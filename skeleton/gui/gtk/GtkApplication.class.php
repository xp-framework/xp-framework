<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.log.Logger', 'util.log.FileAppender');
 
  /**
   * Base application class
   *
   * Example program (does nothing, simply shows an empty window)
   * <code>
   *   $app= &new GTKApplication('Example');
   *   $app->init();
   *   $app->run();
   *   $app->done();
   * </code>
   *
   * @see   php-gtk://GtkWindow
   * @see   http://gtk.org/
   */
  class GtkApplication extends Object {
    var 
      $name     = __CLASS__,
      $window   = NULL,
      $cat      = NULL,
      $rcfile   = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string name default 'gtkapplication' application name
     */
    function __construct($name= __CLASS__) {
      $this->name= $name;
      
      // Set up logger
      $l= &Logger::getInstance();
      $this->cat= &$l->getCategory($this->name);
      $this->cat->identifier= $name;
      $this->cat->addAppender(new FileAppender('php://stderr'));
      
      // Create main window
      $this->create();

      parent::__construct();
    }

    /**
     * Creates the main window
     *
     * @access  protected
     */
    function create() {
      if (!empty($this->rcfile)) Gtk::rc_parse($this->rcfile);
      $this->window= &new GtkWindow();
      $this->window->connect('destroy', array(&$this, 'destroy'));
    }

    /**
     * Initializes the application
     *
     * @model   abstract
     * @access  public
     */
    function init() { }

    /**
     * Is called after the application comes down. Include cleanup
     * code in here.
     *
     * @model   abstract
     * @access  public
     */
    function done() { }

    /**
     * Shows application window and enters main loop.
     *
     * @access  public
     */    
    function run() {
      $this->window->show_all();
      Gtk::main();
    }
    
    /**
     * Callback for when the application is to be closed
     *
     * @access  public
     */       
    function destroy() {
      Gtk::main_quit();
    }
  }
?>
