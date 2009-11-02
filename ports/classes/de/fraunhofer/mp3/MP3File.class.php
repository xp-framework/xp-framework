<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.File', 'de.fraunhofer.mp3.ID3Tag');
  
  /**
   * MP3 file
   *
   * @purpose  Represent an MP3 file
   * @see      http://www.rit.edu/~jlh0956/projects/jd3lib/ 
   */
  class MP3File extends Object {
  
    /**
     * Constructor
     *
     * @param   io.File file
     */
    public function __construct($file) {
      $this->file= $file;
      
    }
    
    /**
     * Extract ID3 Tags
     *
     * @param   int version default ID3_VERSION_UNKNOWN ID3 Version
     * @return  de.fraunhofer.mp3.ID3Tag tag object or NULL if no tag exists
     * @see     http://www.id3.org/
     */
    public function getID3Tag($version= ID3_VERSION_UNKNOWN) {
      try {
        $this->file->open(FILE_MODE_READ);
        
        $done= FALSE;
        do {
          switch ($version) {
            case ID3_VERSION_UNKNOWN:
            
              // Check version 2.x
              $this->file->rewind();
              $buf= $this->file->read(10);
              if ('ID3' == substr($buf, 0, 3)) {
                $version= ID3_VERSION_2;
                break;
              }
              
              // Check version 1.x
              $this->file->seek(-128, SEEK_END);
              $buf= $this->file->read(128);
              $version= ID3_VERSION_1;
              break;

            case ID3_VERSION_1:
            case ID3_VERSION_1_1:
              if (!isset($buf)) {
                $this->file->seek(-128, SEEK_END);
                $buf= $this->file->read(128);
              }
              if ('TAG' == substr($buf, 0, 3)) {
                $version= ("\0" == $buf{125} && "\0" != $buf{126}) ? ID3_VERSION_1_1 : ID3_VERSION_1;
              } else {
                $version= FALSE;
              }
              $done= TRUE;
              break;

            case ID3_VERSION_2:
              if (!isset($buf)) {
                $buf= $this->file->read(3);
                if ('ID3' != substr($buf, 0, 3)) {
                  $version= FALSE;
                } else {
                  // TBD: Implement!
                }
              }
              
              $done= TRUE;
              break;

            default:
              $done= TRUE;
              throw new IllegalArgumentException('Version '.$version.' not supported');
          }
        } while (!$done);
      } catch (Throwable $e) {
        $this->file->close();
        throw $e;
      }
      $this->file->close();
      return (FALSE === $version) ? NULL : ID3Tag::fromString($buf, $version);
    }
  }
?>
