<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.gnome.GtkGladeApplication');
  
  /**
   * Dialog window
   *
   * @purpose  Base class
   */
  class GtkGladeDialogWindow extends GtkGladeApplication {
    var
      $initialized  = FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string gladefile
     * @param   string windowname
     */
    function __construct($gladefile, $windowname) {
      parent::__construct($p= NULL, $gladefile, $windowname);
    }
      
    /**
     * Creates the main window
     *
     * @access  protected
     */
    function create() {
      parent::create();
      $this->window->connect('delete-event', array(&$this, 'destroy'));
    }
    
    /**
     * Changes this dialog's modal state
     *
     * @access  public
     * @param   bool modal
     */
    function setModal($modal) {
      $this->window->set_modal($modal);
    }
    
    /**
     * Show this dialog window
     *
     * @access  public
     * @return  bool
     */
    function show() {
      if (!$this->initialized) {
        try(); {
          $this->init();
        } if (catch('GuiException', $e)) {
          $this->cat->error($e->getStackTrace());
          return FALSE;
        }
        $this->initialized= TRUE;
      }
      $this->run();
      return TRUE;
    }
    
    /**
     * Close (hide) this window
     *
     * @access  public
     */
    function close() {
      $this->window->hide_all();
      if ($this->initialized) Gtk::main_quit();
    }
    
    /**
     * Callback for when the window is about to be destroyed.
     *
     * @access  public
     */       
    function destroy() {
      $this->close();
      return TRUE;
    }
  }
?>
