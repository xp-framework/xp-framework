<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.Streams', 'peer.http.FormData');

  /**
   * File Data; represents single parts of a multipart/form-data request
   */
  class FileUpload extends FormData {
    const CRLF = "\r\n";

    protected
      $name         = NULL,
      $filename     = NULL,
      $stream       = NULL,
      $contentType  = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string filename
     * @param   io.streams.InputStream stream
     * @param   string contentType
     * @param   string encoding
     */
    public function __construct($name, $filename, $stream, $contentType= 'application/octet-stream') {
      $this->name= $name;
      $this->filename= $filename;
      $this->stream= $stream;
      $this->contentType= $contentType;
    }
    
    /**
     * Retrieve string representation of part
     *
     * @return  string
     */
    public function getData() {
      $bytes= Streams::readAll($this->stream);

      // Create headers
      $headers= '';
      $headers.= 'Content-Disposition: form-data; name="'.$this->name.'"; filename="'.$this->filename.'"'.self::CRLF;
      $headers.= 'Content-Type: '.$this->contentType.self::CRLF;
      $headers.= 'Content-Length: '.strlen($bytes).self::CRLF;

      // Return payload      
      return $headers.self::CRLF.$bytes;
    }
  }
?>
