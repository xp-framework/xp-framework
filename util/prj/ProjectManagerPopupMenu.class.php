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
    
    function __construct(&$parent) {
      $this->parent= &$parent;
     
      parent::__construct();
    }

    function addFile(&$menuItem, &$event) {
      $dlg=  &new FileDialog();
      if ($dlg->show ()) {
        $phpFile= &new PHPParser ($dlg->getDirectory().$dlg->getFilename());
        return $this->parent->addFile ($phpFile);
      }
      
      return TRUE;
    }
    
    function reparse() {
      $this->parent->reparse();
    }
  }

?>
