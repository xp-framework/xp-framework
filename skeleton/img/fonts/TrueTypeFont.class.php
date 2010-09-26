<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * True type font
   *
   * @see      php://imagettftext
   * @see      xp://img.shapes.Text
   * @purpose  Font
   */
  class TrueTypeFont extends Object {
    public
      $name=            '',
      $size=            0.0,
      $angle=           0.0,
      $antialiasing=    TRUE;
      
    /**
     * Locate a font
     *
     * @param   string font
     * @return  string
     */
    protected function locate($font) {
      $windows= strncasecmp(PHP_OS, 'Win', 3) === 0;
    
      // Compile extension list
      $extensions= array('.ttf', '.TTF');
      if (strncasecmp($ext= substr($font, -4, 4), '.ttf', 4) === 0) {
        $font= substr($font, 0, -4);
        $extensions[]= $ext;
      }
      
      // Compose TTF search path
      if ($windows) {
        $search= array('.\\', getenv('WINDIR').'\\fonts\\');
      } else {
        $search= array(
          './',
          '/usr/X11R6/lib/X11/fonts/TrueType/',
          '/usr/X11R6/lib/X11/fonts/truetype/',
          '/usr/X11R6/lib/X11/fonts/TTF/',
          '/usr/share/fonts/TrueType/',
          '/usr/share/fonts/truetype/',
          '/usr/openwin/lib/X11/fonts/TrueType/'
        );
      }
      
      // Check for absolute filenames
      if ((DIRECTORY_SEPARATOR === $font{0}) || ($windows && 
        strlen($font) > 1 && (':' === $font{1} || '/' === $font{0})
      )) {
        array_unshift($search, dirname($font).DIRECTORY_SEPARATOR);
        $font= basename($font);
      }
      
      // Search
      foreach ($search as $dir) {
        foreach ($extensions as $ext) {
          if (file_exists($q= $dir.$font.$ext)) return $q;
        }
      }

      throw new IllegalArgumentException('Could not locate font "'.$font.'['.implode(', ', $extensions).']" in '.xp::stringOf($search));
    }
      
    /**
     * Constructor
     *
     * @param   string name the truetype font's name
     * @param   float size default 10.0
     * @param   float angle default 0.0
     * @throws  lang.IllegalArgumentException if the font cannot be found
     */ 
    public function __construct($name, $size= 10.0, $angle= 0.0) {
      $this->name= $this->locate($name);
      $this->size= $size;
      $this->angle= $angle;
    }
    
    /**
     * Draw function
     *
     * @param   resource hdl an image resource
     * @param   img.Color col
     * @param   string text
     * @param   int x
     * @param   int y
     */
    public function drawtext($hdl, $col, $text, $x, $y) {
      return imagettftext(
        $hdl,
        $this->size,
        $this->angle,
        $x,
        $y,
        $col->handle * ($this->antialiasing ? 1 : -1),
        $this->name,
        $text
      );
    }
  }
?>
