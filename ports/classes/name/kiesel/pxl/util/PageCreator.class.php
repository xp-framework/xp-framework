<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'io.File',
    'io.Folder'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PageCreator extends Object {
    public
      $title        = '',
      $description  = '',
      $author       = '',
      $picturefiles = array(),
      $date         = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */

    public function __construct($title, $pictures) {
      $this->title= $title;
      $this->picturefiles= $pictures;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setDate($date) {
      $this->date= $date;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function addPage() {
    }
  }
?>
