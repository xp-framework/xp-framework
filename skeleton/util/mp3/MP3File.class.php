<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.File', 'util.mp3.ID3Tag');
  
  /**
   * MP3 file
   *
   *
   */
  class MP3File extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
      $this->file= &$file;
      parent::__construct();
    }
    
    /**
     * Extract ID3 Tags
     *
     * @access  public
     * @param   int version default ID3_VERSION_1 ID3 Version
     * @see     http://www.id3.org/
     */
    function getID3Tag($version= ID3_VERSION_1) {
      switch ($version) {
        case ID3_VERSION_1:
          try(); {
            $this->file->open(FILE_MODE_READ);
            $this->file->seek(-128, SEEK_END);
            $buf= $this->file->read(128);
            $this->file->close();
          } if (catch('Exception', $e)) {
            return throw($e);
          }
          break;
        
        default:
          return throw(new IllegalArgumentException('Version '.$version.' not supported'));
      }
      
      return ID3Tag::fromString($buf, $version);
    }
  }
?>
