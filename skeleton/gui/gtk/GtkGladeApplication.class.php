<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('gui.gtk.GtkApplication');

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
  class GtkGladeApplication extends GtkApplication {
    var 
      $glade        = NULL,
      $mainwin      = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string name application name
     * @param   string gladefile location of the .glade-file
     * @param   string mainwin default 'window1' Name des Hauptfensters
     */
    function __construct($name, $gladefile, $mainwin= 'window1') {
      $this->glade= &new GladeXML($gladefile);
      $this->mainwin= $mainwin;
      parent::__construct($name);
    }

    /**
     * Returns a widget from the glade file
     *
     * @access  public
     * @param   string name
     * @return  &php.GtkWidget
     */
    function &widget($name) {
      return $this->glade->get_widget($name);
    }

    /**
     * Creates the main window
     *
     * @access  protected
     */
    function create() {
      if (!empty($this->rcfile)) Gtk::rc_parse($this->rcfile);
      $this->window= &$this->widget($this->mainwin);
    }
  }
?>
