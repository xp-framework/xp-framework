<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'io.FileUtil',
    'util.MimeType',
    'peer.mail.util.ImageLoader'
  );

  /**
   * Loads images from the filesystem
   *
   * @purpose  ImageLoader
   */
  class FilesystemImageLoader extends Object implements ImageLoader {

    /**
     * Load an image
     *
     * @param   peer.URL source
     * @return  string[2] data and contenttype
     */
    public function load($source) { 
      return array(
        FileUtil::getContents(new File($source->getURL())),
        MimeType::getByFilename($source->getURL())
      );
    }
  
  } 
?>
