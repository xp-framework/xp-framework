<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.thekid.dialog.AlbumImage',
    'io.File',
    'io.Folder',
    'img.util.ExifData',
    'img.Image',
    'img.Color',
    'img.io.StreamReader',
    'img.io.JpegStreamWriter'
  );

  /**
   * Processes images, creating the "full" and thumbnail versions of
   * specified files and extracting their EXIF data.
   *
   * @purpose  Utility class
   */
  class ImageProcessor extends Object {
    var
      $outputFolder     = NULL,
      $cat              = NULL,
      $thumbDimensions  = array(150, 113);

    /**
     * Set outputFolder
     *
     * @access  public
     * @param   &io.Folder outputFolder
     */
    function setOutputFolder(&$outputFolder) {
      $this->outputFolder= &$outputFolder;
    }

    /**
     * Get outputFolder
     *
     * @access  public
     * @return  &io.Folder
     */
    function &getOutputFolder() {
      return $this->outputFolder;
    }
    
    /**
     * Helper method to create thumbnail from origin image.
     *
     * @access  protected
     * @param   &img.Image origin
     * @param   &img.util.ExifData exifData
     * @return  &img.Image
     */
    function thumbImageFor(&$origin, &$exifData) {
      $this->cat && $this->cat->debug('Resampling thumb-view to', implode('x', $this->thumbDimensions));
      
      with ($thumb= &Image::create($this->thumbDimensions[0], $this->thumbDimensions[1], IMG_TRUECOLOR)); {
        $factor= $origin->getHeight() / $thumb->getHeight();
        $border= intval(($thumb->getWidth() - $origin->getWidth() / $factor) / 2);
        if ($border > 0) {
          $thumb->fill($thumb->allocate(new Color('#ffffff')));
        }
        $thumb->resampleFrom($origin, $border, 0, 0, 0, $thumb->getWidth() - $border - $border);
      }

      return $thumb;
    }

    /**
     * Helper method to create "full" image from origin image.
     *
     * @access  protected
     * @param   &img.Image origin
     * @param   &img.util.ExifData exifData
     * @return  &img.Image
     */
    function fullImageFor(&$origin, &$exifData) {
      $dimensions= $exifData->isHorizontal() ? array(640, 480) : array(480, 640);
      
      $aspect= $origin->getWidth() / $origin->getHeight();
      if ($aspect > 1.0 && $exifData->isVertical()) {
        $this->cat && $this->cat->warn('Image is vertically oriented but its dimensions suggest otherwise');
        $dimensions= array(640, 480);
      }
      
      $this->cat && $this->cat->debug('Resampling full-view to', implode('x', $dimensions));

      with ($full= &Image::create($dimensions[0], $dimensions[1], IMG_TRUECOLOR)); {
        $full->resampleFrom($origin);
      }

      return $full;
    }
          
    /**
     * Returns an album image for a given filename
     *
     * @access  public
     * @param   string filename
     * @return  &de.thekid.dialog.AlbumImage
     * @throws  img.ImagingException in case of an error
     */
    function &albumImageFor($filename) {
      with ($image= &new AlbumImage(basename($filename))); {
        $in= &new File($filename);

        // Read the image's EXIF data
        $this->cat && $this->cat->debug('Extracting EXIF data from', $filename);        
        try(); {
          $image->exifData= &ExifData::fromFile($in);
        } if (catch('ImagingException', $e)) {
          $this->cat && $this->cat->error($e);
          return throw($e);
        }

        $thumbFile= &new File($this->outputFolder->getURI().'thumb.'.$in->getFilename());
        $fullFile= &new File($this->outputFolder->getURI().$in->getFilename());
        
        if ($thumbFile->exists() && $fullFile->exists()) {
          $this->cat && $this->cat->debug('Image has been processed before, skipping...');
        } else {

          // Load origin image
          $this->cat && $this->cat->debug('Loading', $filename);        
          try(); {
            $origin= &Image::loadFrom(new StreamReader($in));
          } if (catch('ImagingException', $e)) {
            $this->cat && $this->cat->error($e);
            return throw($e);
          }

          // Create two resampled versions, one at small dimensions for the
          // overview pages..
          $thumb= &$this->thumbImageFor($origin, $image->exifData);

          // ... and one at 640 x 480 or 480 x 640 (depending on the image 
          // orientation) for the close-up view.
          $full= &$this->fullImageFor($origin, $image->exifData);

          // Write both version to the output directory
          $this->cat && $this->cat->debug('Saving to', $this->outputFolder->getURI());
          try(); {
            $thumb->saveTo(new JpegStreamWriter($thumbFile));
            $full->saveTo(new JpegStreamWriter($fullFile));
          } if (catch('ImagingException', $e)) {
            $this->cat && $this->cat->error($e);
          } finally(); {
            delete($origin);
            delete($thumb);
            delete($full);
            if ($e) return throw($e);
          }
        }
      }

      return $image;
    }
    
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
