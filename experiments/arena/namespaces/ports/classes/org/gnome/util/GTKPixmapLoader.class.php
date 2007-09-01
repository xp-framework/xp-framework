<?php
/* This class is part of the XP framework
 *
 * $Id: GTKPixmapLoader.class.php 9071 2007-01-03 11:38:54Z kiesel $
 */

  namespace org::gnome::util;
 
  /**
   * Pixmap loader
   * 
   * Pixmaps will be loaded into a struct like this:
   * <pre>
   *   ['p:open'] => pixmap_of_open_pixmap,
   *   ['m:open'] => mask_of_open_pixmap,
   *   ['p:quit'] => pixmap_of_quit_pixmap,
   *   ['m:quit'] => mask_of_quit_pixmap
   * </pre>
   *
   * Transparent color will default to black (RGB {0, 0, 0})
   *
   * @test     xp://net.xp_framework.unittest.runner.gtk.UnitTestUI
   * @purpose  Pixmap loader
   */
  class GTKPixmapLoader extends lang::Object {
    public 
      $windowRef,
      $baseDir,
      $transparentColor;

    /**
     * Constructor
     *
     * @param   GtkWindow window a valid window object
     * @param   string baseDir default '.' base directory for pixmaps
     */      
    public function __construct($window, $baseDir= '.') {
      $this->setWindowRef($window);
      $this->setBase($baseDir);
      $this->setTransparentColor(new (0, 0, 0));
      
    }
    
    /**
     * Sets window reference
     *
     * @param   GtkWindow window a valid window object
     */
    public function setWindowRef($window) {
      $this->windowRef= $window;
    }
    
    /**
     * Sets basedir for pixmaps
     *
     * @param   string base base directory
     */
    public function setBase($base) {
      $this->baseDir= $base;
    }
    
    /**
     * Sets transparent color
     *
     * @param   GdkColor color the color to be transparent
     */
    public function setTransparentColor($color) {
      $this->transparentColor= $color;
    }
    
    /**
     * Loads a pixmap into a container
     *
     * @param   &array container
     * @param   string name
     */
    protected function _load($container, $name) {
      list(
        $container['p:'.$name],
        $container['m:'.$name]
      )= ::create_from_xpm(
        $this->windowRef, 
        $this->transparentColor,
        $this->baseDir.DIRECTORY_SEPARATOR.$name.'.xpm'
      );
    }
    
    /**
     * Loads one or more pixmaps
     *
     * @param   mixed names Either a string or an array of strings containinig the 
     *          names of the pixmaps to be loaded (w/o trailing .xpm!)
     * @return  &array pixmaps
     */
    public function load($names) {
      $container= array();
      if (is_string($names)) $names= array($names);
      foreach ($names as $name) {
        $this->_load($container, $name);
      }
      
      return $container;
    }
  }
?>
