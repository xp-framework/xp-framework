<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'gui.gtk.widgets.GtkMenuWidget',
    'gui.gtk.widgets.FileDialog'
  );

  class ProjectManagerPopupMenu extends GtkMenuWidget {
    var
      $parent=    NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
    }

    /**
     * Sets the parent object (needed for callbacks)
     *
     * @access  public
     * @param   &Object
     */
    function setParent(&$parent) {
      $this->parent= &$parent;
    }

    /**
     * addFile callback
     *
     * @access  public
     * @param   &GtkMenuItem
     * @param   &GdkEvent
     * @return  boolean success
     */
    function addFile(&$menuItem, &$event) {
      $dlg=  &new FileDialog();
      if ($dlg->show ()) {
        $phpFile= &new PHPParser ($dlg->getDirectory().$dlg->getFilename());
        return $this->parent->addFile ($phpFile);
      }
      
      return TRUE;
    }

    /**
     * Initiates reparing
     *
     * @access  public
     */    
    function reparse() {
      $this->parent->reparse();
    }
  }

?>
