<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
 
  /**
   * Represents an FDF object. "FDF" stands for "Forms Data Format", 
   * Adobe's way of filling in form fields in PDF files.
   *
   * Example:
   * <code>
   *   $fdf= &new FDF();
   *   $fdf->setReferenceUri('demo1.pdf');
   *   $fdf->setValue('date', date('Y-m-d'));
   *   $fdf->setValue('time', date('H:i:s'));
   * 
   *   $fdf->saveTo(new File('datetime.fdf'));
   * </code>
   *
   * @see      http://www.planetpdf.com/developer/article.asp?contentid=6623&ra
   * @see      http://www.planetpdf.com/developer/article.asp?contentid=6492&ra
   * @purpose  FDF Creation
   */
  class FDF extends Object {
    var
      $version = '1.2',
      $ruri    = '',
      $fields  = array();

    /**
     * Set a value
     *
     * @access  public
     * @param   string key
     * @param   string value
     */
    function setValue($key, $value) {
      $this->fields[$key]= $value;
    }

    /**
     * Set reference URI (a file name or http:// - URL to the PDF file
     * to which this FDF should be applied).
     *
     * @access  public
     * @param   string uri
     */
    function setReferenceUri($uri) {
      $this->ruri= $uri;
    }

    /**
     * Set version. Default version is "1.2".
     *
     * @access  public
     * @param   string str
     */
    function setVersion($version) {
      $this->version= $version;
    }
    
    /**
     * Escapes a string for use in FDF sourcecode
     *
     * @access  protected
     * @param   string str
     * @return  string escaped string
     */
    function _escape($str) {
      return strtr($str, array(
        '\\' => '\\\\',
        '('  => '\\(',
        ')'  => '\\)'
      ));
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= $this->getClassName().'@(version= '.$this->version.', referenceUri= '.$this->ruri.") {\n";
      foreach ($this->fields as $key => $value) {
        $s.= sprintf("  [%-20s] %s\n", $key, xp::stringOf($value));
      }
      return $s.'}';
    }
  
    /**
     * Save this FDF to a stream
     *
     * @access  public
     * @param   &io.Stream stream
     * @return  int number of bytes written
     * @throws  io.IOException
     */
    function saveTo(&$stream) {
      $stream->open(STREAM_MODE_WRITE);
      $bytes= 0;
      
      // Header and version, add object
      $bytes+= $stream->write('%FDF-'.$this->version."\n1 0 obj\n");
      
      // Add dictionary
      $bytes+= $stream->write("<</FDF\n <<");
      
      // Named fields
      $bytes+= $stream->write("/Fields [\n");
      foreach ($this->fields as $key => $value) {
        $bytes+= $stream->write('<< /V ('.$this->_escape($value).')/T ('.$this->_escape($key).") >>\n");
      }
      $bytes+= $stream->write("]\n");

      // Referenced file
      $bytes+= $stream->write('/F ('.$this->_escape($this->ruri).")\n");
      
      // End dictionary
      $bytes+= $stream->write(">>\n");
      
      // End object declaration, append trailer and EOF marker
      $bytes+= $stream->write(">> endobj\ntrailer\n<<\n/Root 1 0 R\n>>\n%%EOF\n");

      $stream->close();
      return $bytes;
    }
  }
?>
