<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.gnome.widgets.GtkMenuWidget',
    'org.gnome.widgets.FileDialog'
  );

  class ProjectManagerPopupMenu extends GtkMenuWidget {
    public
      $parent=    NULL;
    
    /**
     * Sets the parent object (needed for callbacks)
     *
     * @access  public
     * @param   &Object
     */
    public function setParent(&$parent) {
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
    public function addFile(&$menuItem, &$event) {
      if ($this->parent->dialog->show ()) {
      
        // OK pressed and file selected
        return $this->parent->addFile(new PHPParser(
          $this->parent->dialog->getDirectory().
          $this->parent->dialog->getFilename()
        ));
      }
      
      return TRUE;
    }

    /**
     * Initiates reparing
     *
     * @access  public
     */    
    public function reparse() {
      $this->parent->reparse();
    }
  }

?>
