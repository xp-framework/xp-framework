<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.document-root.mono.MonoPicture',
    'io.Folder',
    'io.File',
    'io.FileUtil'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoPictureScanner extends Object {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setPath($path) {
      $this->path= $path;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function create() {
      $shotf= &new Folder($this->path);

      $filename= NULL;
      while ($search= $shotf->getEntry()) {
        if (preg_match('/jpe?g/i', $search)) {
          $filename= $search;
          break;
        }
      }

      if (!$filename) { return NULL; }

      $pic= &new MonoPicture();
      $pic->setFileName($filename);

      // Check for exif-data separately or extract them from the picture
      if (file_exists($exiffile= $shotf->getURI().'/'.basename($filename).'exif')) {
        $pic->setExif(unserialize(FileUtil::getContents(new File($exiffile))));
      } else {
        try(); {
          $exif= &ExifData::fromFile(new File($shotf->getURI().'/'.$filename));
        } if (catch('ImagingException', $e)) {
          $exif= NULL;
        }

        $pic->setExif($exif);
      }
      
      list($width, $height)= getimagesize($shotf->getURI().'/'.$filename);
      $pic->setWidth($width);
      $pic->setHeight($height);
      
      return $pic;
    }
  }
?>
