<?php
/* This class is part of the XP framework
 *
 * $Id: FilesystemImageLoader.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::mail::util;

  ::uses(
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
  class FilesystemImageLoader extends lang::Object implements ImageLoader {

    /**
     * Load an image
     *
     * @param   peer.URL source
     * @return  string[2] data and contenttype
     */
    public function load($source) { 
      return array(
        io::FileUtil::getContents(new io::File($source->getURL())),
        util::MimeType::getByFilename($source->getURL())
      );
    }
  
  } 
?>
