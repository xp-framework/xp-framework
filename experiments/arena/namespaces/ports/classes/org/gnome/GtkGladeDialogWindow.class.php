<?php
/* This class is part of the XP framework
 *
 * $Id: GtkGladeDialogWindow.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::gnome;

  ::uses('org.gnome.GtkGladeApplication');
  
  /**
   * Dialog window
   *
   * @purpose  Base class
   */
  class GtkGladeDialogWindow extends GtkGladeApplication {
    public
      $initialized  = FALSE;

    /**
     * Constructor
     *
     * @param   string gladefile
     * @param   string windowname
     */
    public function __construct($gladefile, $windowname) {
      parent::__construct($p= NULL, $gladefile, $windowname);
    }
      
    /**
     * Creates the main window
     *
     */
    public function create() {
      parent::create();
      $this->window->connect('delete-event', array($this, 'destroy'));
    }
    
    /**
     * Changes this dialog's modal state
     *
     * @param   bool modal
     */
    public function setModal($modal) {
      $this->window->set_modal($modal);
    }
    
    /**
     * Show this dialog window
     *
     * @return  bool
     */
    public function show() {
      if (!$this->initialized) {
        try {
          $this->init();
        } catch ( $e) {
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
     */
    public function close() {
      $this->window->hide_all();
      if ($this->initialized) ::main_quit();
    }
    
    /**
     * Callback for when the window is about to be destroyed.
     *
     */       
    public function destroy() {
      $this->close();
      return TRUE;
    }
  }
?>
