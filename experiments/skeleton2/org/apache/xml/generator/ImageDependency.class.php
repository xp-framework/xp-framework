<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.generator.Dependency',
    'img.util.ImageGeometry'
  );

  /**
   * Image dependency
   *
   * @purpose  Dependency
   */
  class ImageDependency extends Dependency {
  
    /**
     * Process this image dependency
     *
     * @access  public
     * @param   string params
     * @return  string xml
     */
    public function process($params) {
      list($width, $height)= explode('x', $params);
      
      // Retrieve image geometry
      $i= new ImageGeometry($this->name);
      try {
        $dim= $i->getDimensions();
      } catch (XPException $e) {
        throw ($e);
      }
      
      // Overwrite with given parameters if these aren't empty
      if (!empty($width)) $dim[0]= $width;
      if (!empty($height)) $dim[1]= $height;
      
      return vsprintf('<geometry width="%d" height="%d"/>', $dim);
    }
  }
?>
