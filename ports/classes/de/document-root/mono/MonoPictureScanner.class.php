<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.document-root.mono.MonoPicture',
    'de.document-root.mono.MonoPictureScannerException',
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'xml.parser.XMLParser'
  );

  /**
   * Scan a directory for a mono shot and create a
   * MonoPicture object for it.
   *
   * @see      xp://de.document-root.mono.MonoPicture
   * @purpose  purpose
   */
  class MonoPictureScanner extends Object {
    
    /**
     * Sets the path to scan
     *
     * @access  public
     * @param   string path
     */
    function setPath($path) {
      $this->path= $path;
    }

    /**
     * Create a MonoPicture object from the directory. Performs all
     * necessary validity checks and refuses to build object if one
     * check fails.
     *
     * @access  public
     * @return  &de.document-root.mono.MonoPicture
     * @throws  &de.document-root.mono.MonoPictureScannerException if any check fails
     */
    function &create() {
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
      
      // Load title
      $titlefile= &new File($shotf->getUri().'/title.txt');
      if (!$titlefile->exists()) {
        return throw(new MonoPictureScannerException('No title file found for shot #'.basename($shotf->getUri())));
      }
      
      $descfile= &new File($shotf->getUri().'/description.txt');
      if (!$descfile->exists()) {
        return throw(new MonoPictureScannerException('!--> No description file found for shot #'.basename($shotf->getUri())));
      }
      
      try(); {
        $pic->setTitle(FileUtil::getContents($titlefile));
        $pic->setDescription(FileUtil::getContents($descfile));
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    
      try(); {
        $parser= &new XMLParser();
        $parser->parse('<xml>'.$pic->getDescription().'</xml>');
      } if (catch('XMLFormatException', $e)) {
        return throw(new MonoPictureScannerException('Description contains invalid XML for shot #'.basename($shotf->getUri())));
      }

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
