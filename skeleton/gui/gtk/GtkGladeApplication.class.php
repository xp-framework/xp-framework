<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('gui.gtk.GtkApplication');

  /**
   * GTK Application class using glade
   *
   * @ext      gtk
   * @ext      gtk.glade
   * @see      xp://gui.gtk.GtkApplication
   * @see      http://glade.gnome.org/
   * @purpose  Base class
   */  
  class GtkGladeApplication extends GtkApplication {
    var 
      $glade        = NULL,
      $mainwin      = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString paramstring
     * @param   string gladefile location of the .glade-file
     * @param   string mainwin default 'window1' Name des Hauptfensters
     */
    function __construct(&$p, $gladefile, $mainwin= 'window1') {
      $this->glade= &new GladeXML($gladefile);
      $this->mainwin= $mainwin;
      parent::__construct($p);
    }

    /**
     * Returns a widget from the glade file
     *
     * @access  protected
     * @param   string name
     * @return  &php.GtkWidget
     */
    function &widget($name) {
      if (!$w= &$this->glade->get_widget($name)) {
        return throw(new WidgetNotFoundException($name));
      } 
      return $w;
    }

    /**
     * Creates the main window
     *
     * @access  protected
     */
    function create() {
      $this->window= &$this->widget($this->mainwin);
    }
  }
?>
