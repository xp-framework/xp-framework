<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('gui.gtk.GtkGladeApplication');
  
  /**
   * Dialog window
   *
   * @purpose  Base class
   */
  class GtkGladeDialogWindow extends GtkGladeApplication {
    
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
     * Show this dialog window
     *
     * @access  public
     * @return  bool
     */
    function show() {
      static $initialized= FALSE;
      
      if (!$initialized) {
        $this->init();
        $initialized= TRUE;
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
      $this->cat->debug('close');
      $this->window->hide_all();
      Gtk::main_quit();
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
