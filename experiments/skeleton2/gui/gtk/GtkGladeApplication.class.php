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
    public
      $glade        = NULL,
      $mainwin      = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString paramstring
     * @param   string gladefile location of the .glade-file
     * @param   string mainwin default 'window1'
     */
    public function __construct($p, $gladefile, $mainwin= 'window1') {
      if (!$this->glade= new GladeXML($gladefile)) {
        throw (new GuiException('Cannot read glade file '.$gladefile));
      }
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
    protected function widget($name) {
      if (!$this->glade || !$w= $this->glade->get_widget($name)) {
        throw (new WidgetNotFoundException($name));
      } 
      return $w;
    }

    /**
     * Creates the main window
     *
     * @access  protected
     */
    protected function create() {
      $this->window= self::widget($this->mainwin);
    }
  }
?>
