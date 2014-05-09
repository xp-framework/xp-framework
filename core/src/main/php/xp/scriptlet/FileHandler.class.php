<?php namespace xp\scriptlet;

use io\File;
use io\Folder;
use io\IOException;
use util\MimeType;
use peer\Socket;

/**
 * File handler
 */
class FileHandler extends AbstractUrlHandler {
  protected $docroot= '';

  /**
   * Constructor
   *
   * @param   string docroot document root
   * @param   var notFound what to do if file is not found (default: send error)
   */
  public function __construct($docroot, $notFound= null) {
    $this->docroot= new Folder($docroot);
    if (null === $notFound) {
      $handler= $this;
      $this->notFound= function($socket, $path) use($handler) {
        return $handler->sendErrorMessage($socket, 404, 'Not found', $path);
      };
    } else {
      $this->notFound= $notFound;
    }
  }

  /**
   * Headers lookup
   *
   * @param   array<string, string> headers
   * @param   string name
   * @return  string
   */
  protected function header($headers, $name) {
    if (isset($headers[$name])) return $headers[$name];
    foreach ($headers as $key => $value) {
      if (0 == strcasecmp($key, $name)) return $value;
    }
    return NULL;
  }

  /**
   * Handle a single request
   *
   * @param   string method request method
   * @param   string query query string
   * @param   [:string] headers request headers
   * @param   string data post data
   * @param   peer.Socket socket
   * @return  int
   */
  public function handleRequest($method, $query, array $headers, $data, Socket $socket) {
    $url= parse_url($query);
    $f= new File($this->docroot, strtr(
      preg_replace('#\.\./?#', '/', urldecode($url['path'])),
      '/',
      DIRECTORY_SEPARATOR
    ));
    if (!is_file($f->getURI())) {
      return call_user_func($this->notFound, $socket, $url['path']);
    }

    // Implement If-Modified-Since/304 Not modified
    $lastModified= $f->lastModified();
    if ($mod= $this->header($headers, 'If-Modified-Since')) {
      $d= strtotime($mod);
      if ($lastModified <= $d) {
        return $this->sendHeader($socket, 304, 'Not modified', array());
      }
    }

    try {
      $f->open(FILE_MODE_READ);
    } catch (IOException $e) {
      $this->sendErrorMessage($socket, 500, 'Internal server error', $e->getMessage());
      $f->close();
      return;
    }

    // Send OK header and data in 8192 byte chunks
    $sc= $this->sendHeader($socket, 200, 'OK', array(
      'Last-Modified'   => gmdate('D, d M Y H:i:s T', $lastModified),
      'Content-Type'    => MimeType::getByFileName($f->getFilename()),
      'Content-Length'  => $f->size(),
    ));
    while (!$f->eof()) {
      $socket->write($f->read(8192));
    }
    $f->close();
    return $sc;
  }

  /**
   * Returns a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->docroot->toString().', '.\xp::stringOf($this->notFound).'>';
  }
}
