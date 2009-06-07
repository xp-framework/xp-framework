<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('org.gnu.tar.TarArchiveEntry');

  /**
   * Kapselt ein Tar-Archiv
   *
   * Usage:
   * <code>
   *   $a= new TarArchive(new File('foo.tar.gz'));
   *   $a->open(FILE_MODE_READ);
   *   $entry= $a->getEntry();
   *   $a->close();
   *
   *   printf("Filesize of entry %s is %d bytes\n", $entry->name, $entry->size);
   * </code>
   *
   * @see http://www.gnu.org/software/tar/tar.html
   */
  class TarArchive extends Object {
    public
      $file;
    
    /**
     * Constructor
     *
     * @param   io.File file
     */  
    public function __construct($file) {
      $this->file= $file;
    }
    
    /**
     * Open this archive
     *
     * @param   var* args arguments to io.File::open()
     * @return  bool TRUE on success, FALSE otherwise
     */
    public function open() {
      $args= func_get_args();
      return call_user_func_array(array($this->file, 'open'), $args);
    }
    
    /**
     * Close this archive
     *
     * @return  bool TRUE on success, FALSE otherwise
     */
    public function close() {
      return $this->file->close();
    }
    
    /**
     * Get next entry
     *
     * @return  org.gnu.tar.TarArchiveEntry 
     */
    public function getEntry() {
      static $size= 0;
      
      // Am EOF nicht mehr weiterlesen
      if ($this->file->eof()) return FALSE;

      // Zur nächsten Datei vorwärts lesen
      $this->file->seek($this->file->tell()+ ceil($size / 512) * 512);
      
      // Header lesen
      $bin= $this->file->read(512);
      $data= unpack(
        'a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor', 
        $bin
      );
      if ('' == trim($data['filename'])) return FALSE;
      
      $f= new TarArchiveEntry($data, $this->file->tell());
      $size= $f->size;
      return $f;
    }
    
    /**
     * Get an entry's content
     *
     * @param   org.gnu.tar.TarArchiveEntry e
     * @return  string content
     */
    public function getEntryData($e) {
      $this->file->seek($e->offset);
      $content= $this->file->read($e->size);
      $this->file->seek($e->offset);
      return $content;
    }
  }
?>
