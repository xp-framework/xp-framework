<?php
/* This class is part of the XP framework
 *
 * $Id: ProjectManagerPopupMenu.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace de::document-root::gui::gtk::project;

  ::uses(
    'org.gnome.widgets.GtkMenuWidget',
    'org.gnome.widgets.FileDialog'
  );

  class ProjectManagerPopupMenu extends org::gnome::widgets::GtkMenuWidget {
    public
      $parent=    NULL;
    
    /**
     * Sets the parent object (needed for callbacks)
     *
     * @param   &Object
     */
    public function setParent($parent) {
      $this->parent= $parent;
    }

    /**
     * addFile callback
     *
     * @param   &GtkMenuItem
     * @param   &GdkEvent
     * @return  boolean success
     */
    public function addFile($menuItem, $event) {
      if ($this->parent->dialog->show ()) {
      
        // OK pressed and file selected
        return $this->parent->addFile(new text::PHPParser(
          $this->parent->dialog->getDirectory().
          $this->parent->dialog->getFilename()
        ));
      }
      
      return TRUE;
    }

    /**
     * Initiates reparing
     *
     */    
    public function reparse() {
      $this->parent->reparse();
    }
  }

?>
