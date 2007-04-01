<?php
/* This class is part of the XP framework
 *
 * $Id: ArchiveReader.class.php 9090 2007-01-03 13:57:55Z friebe $ 
 */

  define('ARCHIVE_READ',             0x0000);
  define('ARCHIVE_CREATE',           0x0001);
  define('ARCHIVE_HEADER_SIZE',      0x0100);
  define('ARCHIVE_INDEX_ENTRY_SIZE', 0x0100);

  /**
   * Simple lightweight archive reader. This is a reading-only
   * archive class which can be used to minimize class
   * dependencies.
   * 
   * @see      xp://lang.archive.ArchiveClassLoader
   * @purpose  Lightweight archive reader
   */
  class ArchiveReader extends Object {
    public
      $file     = '',
      $version  = 1;
    
    public
      $_index   = array();

    /**
     * Constructor.
     *
     * @param   string filename
     */
    public function __construct($filename) {
      $this->file= $filename;
    }

    /**
     * Get URI
     *
     * @return  string uri
     */
    public function getURI() {
      return $this->file;
    }
    
    /**
     * Check whether a given element exists
     *
     * @param   string id the element's id
     * @return  bool TRUE when the element exists
     */
    public function contains($id) {
      return isset($this->_index[$id]);
    }
    
    /**
     * Get entry (iterative use)
     * <code>
     *   $a= new Archive(new File('port.xar'));
     *   $a->open(ARCHIVE_READ);
     *   while ($id= $a->getEntry()) {
     *     var_dump($id);
     *   }
     *   $a->close();
     * </code>
     *
     * @return  string id or FALSE to indicate the pointer is at the end of the list
     */
    public function getEntry() {
      $key= key($this->_index);
      next($this->_index);
      return $key;
    }

    /**
     * Rewind archive
     *
     */
    public function rewind() {
      $current= XpXarloader::acquire($this->file);
      reset($current['index']);
    }
    
    /**
     * Extract a file's contents
     *
     * @param   string id
     * @return  string content
     */
    public function extract($id) {
      if (!$this->contains($id)) {
        return FALSE;
      }
      
      return file_get_contents('xar://'.$this->file.'?'.$id);
    }
    
    /**
     * Fetches a stream to the file in the archive
     *
     * @param   string id
     * @return  io.Stream
     */
    public function getStream($id) {
      if (!$this->contains($id)) {
        return FALSE;
      }

      return XPClass::forName('io.File')->newInstance('xar://'.$this->file.'?'.$id);
    }
    
    /**
     * Open this archive.
     *
     * Note: this light-weight implementation of an ArchiveReader
     * only supports opening the archive in ARCHIVE_READ mode.
     *
     * @param   int mode default ARCHIVE_READ one of ARCHIVE_READ | ARCHIVE_CREATE
     * @return  bool success
     */
    public function open($mode) {
      switch ($mode) {
        case ARCHIVE_READ:      // Load
          if (FALSE === ($current= XpXarLoader::acquire($this->file))) {
            throw new FormatException('Unable to open archive file "'.$this->file.'"';
          }
          
          $this->_index= $current['index'];
      }
      
      return FALSE;
    }
    
    /**
     * Close this archive
     *
     * @return  bool success
     */
    public function close() {
      return TRUE;
    }
    
    /**
     * Checks whether this archive is open
     *
     * @return  bool TRUE when the archive file is open
     */
    public function isOpen() {
      return (bool)sizeof($this->_index);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(version= %s, index size= %d) { %s }',
        $this->getClassName(),
        $this->version,
        sizeof($this->_index)
      );
    }
  }
?>
