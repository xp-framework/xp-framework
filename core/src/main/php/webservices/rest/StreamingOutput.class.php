<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Output', 'util.MimeType');

  /**
   * Represents a stream to be output
   *
   */
  class StreamingOutput extends webservices·rest·Output {
    protected $inputStream= NULL;
    protected $mediaType= 'application/octet-stream';
    protected $contentLength= 0;
    public $payload= NULL;
  
    /**
     * Creates a new streaming output instance with a given input stream
     *
     * @param  io.streams.InputStream inputStream
     */
    public function __construct(InputStream $inputStream= NULL) {
      $this->inputStream= $inputStream;
    }

    /**
     * Factory method
     *
     * @param  var arg either an InputStream, File, or IOElement
     * @return self
     * @throws lang.IllegalArgumentException
     */
    public static function of($arg) {
      if ($arg instanceof InputStream) {
        return new self($arg);
      } else if ($arg instanceof File) {
        return create(new self($arg->getInputStream()))
          ->withMediaType(MimeType::getByFileName($arg->getFileName()))
          ->withSize($arg->getSize())
        ;
      } else if ($arg instanceof IOElement) {
        return create(new self($arg->getInputStream()))
          ->withMediaType(MimeType::getByFileName($arg->getURI()))
          ->withSize($arg->getSize())
        ;
      } else {
        throw new IllegalArgumentException('Expected either an InputStream, File, or IOElement, have '.xp::typeOf($arg));
      }
    }

    /**
     * Sets statuscode
     *
     * @param  int status HTTP status code
     * @return self
     */
    public function withStatus($status) {
      $this->status= $status;
      return $this;
    }

    /**
     * Sets contentLength. Pass NULL to avoid setting the "Content-Length" header.
     *
     * @param  int length
     * @return self
     */
    public function withContentLength($length) {
      $this->contentLength= $length;
      return $this;
    }

    /**
     * Sets mediaType
     *
     * @param  string type
     * @return self
     */
    public function withMediaType($type) {
      $this->mediaType= $type;
      return $this;
    }

    /**
     * Writes to output. This default implementation will copy data from
     * the input stream while data is available on it.
     *
     * @param  io.streams.OutputStream out
     */
    public function write($out) {
      while ($this->inputStream->available()) {
        $out->write($this->inputStream->read());
      }
    }

    /**
     * Writes this payload to an output stream
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     * @return bool handled
     */
    public function writeTo($response, $base, $format) {
      $response->setStatus($this->status);
      $response->setContentType($this->mediaType);
      if (NULL !== $this->contentLength) {
        $response->setContentLength($this->contentLength);
      }

      // Headers
      foreach ($this->headers as $name => $value) {
        if ('Location' === $name) {
          $url= clone $base;
          $response->setHeader($name, $url->setPath($value)->getURL());
        } else {
          $response->setHeader($name, $value);
        }
      }
      foreach ($this->cookies as $cookie) {
        $response->setCookie($cookie);
      }

      $output= $response->getOutputStream();
      $output->flush();
      $this->write($output);
      return TRUE;
    }
  }
?>
