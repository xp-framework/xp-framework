<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.website.doc.build.storage.DocStorage', 
    'io.FileNotFoundException',
    'io.Folder',
    'io.FileUtil',
    'io.File'
  );

  /**
   * Stores generated 
   *
   * @purpose  DocStorage implementation
   */
  class FileSystemDocStorage extends Object implements DocStorage {
    protected
      $base= NULL;
      
    /**
     * Constructor
     *
     * @param   io.Folder base
     * @throws  io.FileNotFoundException in case the given folder does not exist
     */
    public function __construct(Folder $base) {
      if (!$base->exists()) {
        throw new FileNotFoundException($base->toString().' does not exist!');
      }
      $this->base= $base;
    }
    
    /**
     * Return an entry for a given name
     *
     * @param   string name
     * @return  io.File
     * @throws  util.NoSuchElementException if no element by the given name exists
     */
    protected function entry($name, $create= FALSE) {
      return new File($this->base, $name.'.dat');
    }

    /**
     * Stores an item
     *
     * @param   string name
     * @return  xml.Tree t
     */
    public function store($name, Tree $t) {
      FileUtil::setContents($this->entry($name, TRUE), serialize($t));
    }

    /**
     * Removes an item
     *
     * @param   string name
     * @throws  util.NoSuchElementException if no element by the given name exists
     */
    public function remove($name) {
      try {
        $this->entry($name)->unlink();
      } catch (FileNotFoundException $e) {
        throw new NoSuchElementException('Storage entry "'.$name.'" not found in '.$this->base->toString());
      }
    }
    
    /**
     * Gets an item
     *
     * @param   string name
     * @return  xml.Tree
     * @throws  util.NoSuchElementException if no element by the given name exists
     */
    public function get($name) {
      try {
        return cast(unserialize(FileUtil::getContents($this->entry($name))), 'xml.Tree');
      } catch (FileNotFoundException $e) {
        throw new NoSuchElementException('Storage entry "'.$name.'" not found in '.$this->base->toString());
      }
    }
  }
?>
