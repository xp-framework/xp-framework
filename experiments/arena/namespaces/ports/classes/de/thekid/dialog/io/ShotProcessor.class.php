<?php
/* This class is part of the XP framework
 *
 * $Id: ShotProcessor.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog::io;

  ::uses(
    'img.convert.GrayscaleConverter',
    'de.thekid.dialog.io.ImageProcessor'
  );

  /**
   * Processes a single shot
   *
   * @see      xp://de.thekid.dialog.io.ImageProcessor
   * @purpose  Specialized processor
   */
  class ShotProcessor extends ImageProcessor {
    public
      $converter        = array(),
      $detailDimensions = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->converter['grayscale']= new img::convert::GrayscaleConverter();
      $this->detailDimensions = array(459, 230);
    }
    
    /**
     * Resample a given image to given dimensions. Will always fit the 
     * image into the given dimensions, cutting where necessary
     *
     * @param   &img.Image origin
     * @param   int[2] dimensions (0 = X, 1 = Y)
     * @return  &img.Image
     */
    public function resampleToFixedCut($origin, $dimensions) {
      $this->cat && $this->cat->debug('Resampling image to fixed', implode('x', $dimensions));
      
      with ($resized= img::Image::create($dimensions[0], $dimensions[1], IMG_TRUECOLOR)); {
        $factor= $origin->getWidth() / $resized->getWidth();
        $cut= max(0, intval(((($origin->getHeight() / $factor) - $dimensions[1]) * $factor) / 2));
        
        $this->cat && $this->cat->debug('Need to cut', $cut, 'pixels from original');
        $resized->resampleFrom($origin, 0, 0, 0, $cut, -1, -1, -1, $origin->getHeight()- $cut- $cut);
      }

      return $resized;
    }

    /**
     * Helper method to create detail image from origin image.
     *
     * @param   &img.Image origin
     * @param   &img.util.ExifData exifData
     * @return  &img.Image
     */
    public function detailImageFor($origin, $exifData) {
      return $this->resampleToFixedCut($origin, $this->detailDimensions);
    }

    /**
     * Helper method to create grayscale from origin image.
     *
     * @param   &img.Image origin
     * @param   &img.util.ExifData exifData
     * @return  &img.Image
     */
    public function grayScaleThumbImageFor($origin, $exifData) {
      $resized= $this->resampleToFixedCut($origin, $this->thumbDimensions);
      $resized->convertTo($this->converter['grayscale']);
      return $resized;
    }

    /**
     * Helper method to create grayscale from origin image.
     *
     * @param   &img.Image origin
     * @param   &img.util.ExifData exifData
     * @return  &img.Image
     */
    public function grayScaleFullImageFor($origin, $exifData) {
      $resized= $this->resampleTo($origin, $exifData->isHorizontal(), $this->fullDimensions);
      $resized->convertTo($this->converter['grayscale']);
      return $resized;
    }

    /**
     * Retrieve a list of targets to be transformed
     *
     * @param   &io.File in
     * @return  de.thekid.dialog.io.ProcessorTarget[]
     */
    public function targetsFor($in) {
      return array(
        new ProcessorTarget('detailImageFor', 'detail.'.$in->getFilename(), TRUE),
        new ProcessorTarget('fullImageFor', 'color.'.$in->getFilename(), TRUE),
        new ProcessorTarget('grayScaleFullImageFor', 'gray.'.$in->getFilename(), TRUE),
        new ProcessorTarget('thumbImageFor', 'thumb.color.'.$in->getFilename(), FALSE),
        new ProcessorTarget('grayScaleThumbImageFor', 'thumb.gray.'.$in->getFilename(), FALSE)
      );
    }
  }
?>
