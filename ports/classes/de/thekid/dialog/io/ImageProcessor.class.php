<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.thekid.dialog.AlbumImage',
    'de.thekid.dialog.io.ProcessorTarget',
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
      $filters          = array(),
      $quality          = 90,
      $thumbDimensions  = array(150, 113),
      $fullDimensions   = array(640, 480);

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
     * Add a filter. The filters will be applied in the order added on 
     * an image after processing it.
     *
     * @access  public
     * @param   &img.filter.ImageFilter filter
     * @return  &img.filter.ImageFilter filter
     */
    function &addFilter(&$filter) {
      $this->filters[]= &$filter;
      return $filter;
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
     * Set quality (defaults to 90)
     *
     * @access  public
     * @param   int quality A quality value in percent
     */
    function setQuality($quality) {
      $this->quality= $quality;
    }

    /**
     * Get quality
     *
     * @access  public
     * @return  int
     */
    function getQuality() {
      return $this->quality;
    }
    
    /**
     * Resample a given image to given dimensions.
     *
     * @access  protected
     * @param   &img.Image origin
     * @param   bool horizontal
     * @param   int[2] dimensions (0 = X, 1 = Y)
     * @return  &img.Image
     */
    function resampleTo(&$origin, $horizontal, $dimensions) {
    
      // Check whether the picture is landscape or portrait
      if ($origin->getWidth() < $origin->getHeight()) {
      
        // This is portrait, so flip dimensions to reflect that
        $dimensions= array_reverse($dimensions);
      }
    
      // Find out the maximum divider required, it is used
      // for both dimensions to keep aspect ratio.
      $div= max(
        $origin->getWidth()  / $dimensions[0],
        $origin->getHeight() / $dimensions[1]
      );
      
      $d= array(
        $origin->getWidth()  / $div,
        $origin->getHeight() / $div
      );
    
      $this->cat && $this->cat->infof(
        'Resampling %s image to %d x %d', 
        $horizontal ? 'horizontal' : 'vertical',
        $d[0],
        $d[1]
      );
      $resized= &Image::create($d[0], $d[1], IMG_TRUECOLOR);
      $resized->resampleFrom($origin);
      return $resized;
    }
 
    /**
     * Resample a given image to given dimensions. Will always fit the 
     * image into the given dimensions, adding a border with the specified
     * color if necessary.
     *
     * @access  protected
     * @param   &img.Image origin
     * @param   int[2] dimensions (0 = X, 1 = Y)
     * @param   &img.Color color
     * @return  &img.Image
     */
    function resampleToFixed(&$origin, $dimensions, &$color) {
      $this->cat && $this->cat->debug('Resampling image to fixed', implode('x', $dimensions));
      
      with ($resized= &Image::create($dimensions[0], $dimensions[1], IMG_TRUECOLOR)); {
        $factor= $origin->getHeight() / $resized->getHeight();
        $border= intval(($resized->getWidth() - $origin->getWidth() / $factor) / 2);
        if ($border > 0) {
          $resized->fill($resized->allocate($color));
        }
        $resized->resampleFrom($origin, $border, 0, 0, 0, $resized->getWidth() - $border - $border);
      }

      return $resized;
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
      return $this->resampleToFixed($origin, $this->thumbDimensions, new Color('#ffffff'));
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
      return $this->resampleTo($origin, $exifData->isHorizontal(), $this->fullDimensions);
    }
    
    /**
     * Retrieve a list of targets to be transformed
     *
     * @access  protected
     * @param   &io.File in
     * @return  de.thekid.dialog.io.ProcessorTarget[]
     */
    function targetsFor(&$in) {
      return array(
        new ProcessorTarget('thumbImageFor', 'thumb.'.$in->getFilename()),
        new ProcessorTarget('fullImageFor', $in->getFilename())
      );
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

        // Go over targets
        $origin= NULL;
        foreach ($this->targetsFor($in) as $target) {
          $destination= &new File($this->outputFolder->getURI().$target->getDestination());
          if ($destination->exists()) {
            $this->cat && $this->cat->debugf(
              'Target method %s has been processed before, skipping...',
              $target->getMethod()
            );
            continue;
          }
          
          // If we haven't done so before, load origin image
          if (!isset($origin)) {
            $this->cat && $this->cat->debug('Loading', $filename);        
            try(); {
              $origin= &Image::loadFrom(new StreamReader($in));
            } if (catch('ImagingException', $e)) {
              $this->cat && $this->cat->error($e);
              return throw($e);
            }
          }
          
          // Transform
          $transformed= &$this->{$target->getMethod()}($origin, $image->exifData);
          
          // Apply post-transform filters
          for ($i= 0, $s= sizeof($this->filters); $i < $s; $i++) {
            $this->cat && $this->cat->debugf(
              'Applying filter %d of %d (%s)', 
              $i, 
              $s, 
              $this->filters[$i]->toString()
            );
            $transformed->apply($this->filters[$i]);
          }
          
          // Save
          $this->cat && $this->cat->debug('Saving to', $destination->getURI());
          try(); {
            $transformed->saveTo(new JpegStreamWriter($destination, $this->quality));
          } if (catch('ImagingException', $e)) {
            $this->cat && $this->cat->error($e);
            delete($transformed);
            delete($origin);
            return throw($e);
          }

          delete($transformed);
        }
        
        // Clean up
        delete($origin);
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
