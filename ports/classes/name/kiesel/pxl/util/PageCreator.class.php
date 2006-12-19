<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'name.kiesel.pxl.storage.FilesystemContainer',
    'name.kiesel.pxl.Catalogue',
    'name.kiesel.pxl.Picture',
    'name.kiesel.pxl.Page',
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
      $storage  = NULL,
      $title    = '',
      $description  = '',
      $author       = '',
      $picturefiles = array(),
      $date     = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function __construct(&$storage, $title, $pictures) {
      $this->storage= &$storage;
      $this->title= $title;
      $this->picturefiles= $pictures;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setDate(&$date) {
      $this->date= $date;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function addPage() {
      try {
        $c= &Catalogue::create($this->storage);
      } catch (FileNotFoundException $e) {
        $c= new Catalogue();
        $c->setStorage($this->storage);
      }
      
      // Create filesystem-friendly name
      $dirname= preg_replace('#[^a-zA-Z0-9_]#', '', str_replace(' ', '_', $this->title));
      
      // Create page directory
      $targetdir= $this->storage->getBase().'/'.$dirname;
      
      $folder= new Folder($targetdir);
      try {
        $folder->create(0755);
      } catch (IOException $e) {
        throw($e);
      }
      
      $p= new Page();
      $p->setStorage(new FilesystemContainer($targetdir));
      $p->setTitle($this->title);

      foreach ($this->picturefiles as $filename) {
        try {
          $f= new File($filename);
          if (!$f->exists()) continue;

          // Copy over to new destination
          $f->copy($folder->getURI().'/'.$f->getFileName());

          $picture= new Picture();
          $picture->setFilename($f->getFileName());
          $picture->setStorage($this->storage);
        } catch (IOException $e) {
          continue;
        }
          
        $p->addPicture($picture);
      }
      
      if ($p->pictures->size() == 0) {
        throw(new IllegalStateException('No pictures added to page, cannot add page to index'));
      }
      
      $entry= new CatalogueEntry();
      $entry->setId($c->entries->size());
      $entry->setName($this->title);
      $entry->setPath($dirname);
      $c->addEntry($entry);
      
      $c->hibernate();
      $p->hibernate();
      
      return TRUE;
    }
  }
?>
