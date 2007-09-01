<?php
/* This class is part of the XP framework
 *
 * $Id: TarArchive.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::gnu::tar;
 
  ::uses('org.gnu.tar.TarArchiveEntry');

  /**
   * Kapselt ein Tar-Archiv
   *
   * Usage:
   * <code>
   * $a= &new TarArchive(new File('foo.tar.gz'));
   * $a->open(FILE_MODE_READ);
   * $entry= &$a->getEntry();
   * $a->close();
   *
   * printf("Filesize of entry %s is %d bytes\n", $entry->name, $entry->size);
   * </code>
   *
   * @see http://www.gnu.org/software/tar/tar.html
   */
  class TarArchive extends lang::Object {
    public
      $file;
    
    /**
     * Constructor
     *
     * @param   io.File file File-Objekt
     */  
    public function __construct($file) {
      $this->file= $file;
      
    }
    
    /**
     * Öffnen
     *
     * @param   mixed args Argumente für die open()-Method des Datei-Objekts
     * @return  bool Das Ergebnis der open()-Method des Datei-Objekts
     */
    public function open() {
      $args= func_get_args();
      return call_user_func_array(
        array($this->file, 'open'), 
        $args
      );
    }
    
    /**
     * Schließen
     *
     * @return  bool Das Ergebnis der close()-Method des Datei-Objekts
     */
    public function close() {
      return $this->file->close();
    }
    
    /**
     * Holt sich den nächsten Eintrag aus dem Archiv
     *
     * @return  io.TarArchiveEntry Eintrag 
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
      
      // TarArchiveEntry-Objekt erzeugen
      $f= new TarArchiveEntry(
        $data, 
        $this->file->tell()
      );
      
      // Größe merken, damit beim nächsten Aufruf dieser Funktion automatisch geseek()ed wird
      $size= $f->size;
      
      return $f;
    }
    
    /**
     * Inhalt einer Datei zurückgeben
     *
     * @param   io.TarArchiveEntry e TarArchvieEntry-Objekt
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
