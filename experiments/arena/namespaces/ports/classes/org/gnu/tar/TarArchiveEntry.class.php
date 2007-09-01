<?php
/* This class is part of the XP framework
 *
 * $Id: TarArchiveEntry.class.php 8975 2006-12-27 18:06:40Z friebe $
 */

  namespace org::gnu::tar;

  // Bitmasken für den Dateitypen
  define('TAR_FTYPE_IFDIR',  0040000); // Directory
  define('TAR_FTYPE_IFCHR',  0020000); // Character device
  define('TAR_FTYPE_IFBLK',  0060000); // Block device
  define('TAR_FTYPE_IFREG',  0100000); // Regular file
  define('TAR_FTYPE_IFIFO',  0010000); // FIFO
  define('TAR_FTYPE_IFLNK',  0120000); // Symbolic link
  define('TAR_FTYPE_IFSOCK', 0140000); // Socket  

  /**
   * Kapselt ein Tar-Archiv-Eintrag
   *
   * @see http://www.gnu.org/software/tar/tar.html
   */
  class TarArchiveEntry extends lang::Object {
    public
      $filename,        // Dateiname
      $mode,            // Modus
      $uid,             // User-ID
      $gid,             // Group ID
      $size,            // Größe in Bytes
      $mtime,           // fileModifiedTime
      $checksum,        // Checksumme
      $typeflag,        // Dateityp
      $link,
      $magic,
      $version,
      $uname,
      $gname,
      $devmajor,
      $devminor,
      $offset;
      
    /**
     * Constructor
     *
     * @param   array data Daten aus unpack
     * @param   int offset Offset innerhalb des Archivs
     */
    public function __construct($data, $offset) {
      $this->filename= trim($data['filename']);
      $this->mode= octdec(trim($data['mode']));
      $this->uid= octdec(trim($data['uid']));
      $this->gid= octdec(trim($data['gid']));
      $this->size= octdec(trim($data['size']));
      $this->mtime= octdec(trim($data['mtime']));
      $this->checksum= octdec(trim($data['checksum']));
      $this->typeflag= $data['typeflag'];
      $this->link= trim($data['link'] == '0') ? FALSE : TRUE;
      $this->magic= trim($data['magic']);
      $this->version= trim($data['version']);
      $this->uname= trim($data['uname']);
      $this->gname= trim($data['gname']);
      $this->devmajor= trim($data['devmajor']);
      $this->devminor= trim($data['devminor']);
      $this->offset= $offset;
      
    }
    
    public function getFileTypeString() {
      static $map = array(
        '-' => TAR_FTYPE_IFREG,
        'd' => TAR_FTYPE_IFDIR,
        'l' => TAR_FTYPE_IFLNK,
        'c' => TAR_FTYPE_IFCHR,
        'b' => TAR_FTYPE_IFBLK,
        'p' => TAR_FTYPE_IFIFO,
        's' => TAR_FTYPE_IFSOCK,
      );

      foreach ($map as $char => $mask) if ($this->mode & $mask) return $char;
    }

    public function getUserPermissions() {
      return ($this->mode >> 6) & 7;
    }
    
    public function getGroupPermissions() {
      return ($this->mode >> 3) & 7;
    }

    public function getWorldPermissions() {
      return ($this->mode) & 7;
    }
    
    public function getPermissionString() {
      return sprintf(
        '%s%s%s', 
        $this->_rwx($this->getUserPermissions()),
        $this->_rwx($this->getGroupPermissions()),
        $this->_rwx($this->getWorldPermissions())
      );
    }

    protected function _rwx($bits) {
      $str= '';
      $str.= ($bits & 4) ? 'r' : '-';
      $str.= ($bits & 2) ? 'w' : '-';
      $str.= ($bits & 1) ? 'x' : '-';
      return $str;
    }

  }
?>
