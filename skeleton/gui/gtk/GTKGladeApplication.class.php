<?php  
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('gui.gtk.GTKApplication');

  /**
   * Application class with Glade
   *
   * Example:
   * <code>
   *   $app= &new GTKGladeApplication('Example', 'example.glade');
   *   $app->init();
   *   $app->run();
   *   $app->done();
   * </code>
   * 
   * @see   http://glade.gnome.org/
   */  
  class GTKGladeApplication extends GTKApplication {
    var $glade;
    var $gladeFile;
    var $windowWidget;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name Name der Anwendung
     * @param   string gladeFile Dateiname des .glade-Files
     * @param   string windowWidget default 'window1' Name des Hauptfensters
     */
    function __construct($name, $gladeFile, $windowWidget= 'window1') {
      parent::__construct($name);
      $this->gladeFile= $gladeFile;
      $this->windowWidget= $windowWidget;
    }

    /**
     * Erzeugt das Hauptfenster
     *
     * @access  private
     */
    function _create() {
      if (!isset($this->window)) $this->window= &$this->glade->get_widget($this->windowWidget);
    }

    /**
     * Initialisiert die Anwendung
     *
     * @access  public
     */
    function init() {
      $this->log('init', $this->gladeFile);
      if (isset($this->rcFile)) Gtk::rc_parse($this->rcFile); 
      $this->glade= &new GladeXML($this->gladeFile);
      parent::init();
    }
    
    /**
     * Gibt ein Widget aus dem Glade-File zurück
     *
     * @access  public
     * @param   string name Name des Widgets
     * @return  GtkWidget Das korrespondierende Widget
     */
    function &widget($name) {
      return $this->glade->get_widget($name);
    }
  }
?>

