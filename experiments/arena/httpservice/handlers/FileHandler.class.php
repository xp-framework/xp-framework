<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File', 'util.MimeType', 'handlers.AbstractUrlHandler');

  /**
   * File handler
   *
   * @see      HttpProtocol
   * @purpose  Handler for HttpProtocol
   */
  class FileHandler extends AbstractUrlHandler {
    protected 
      $base= '';

    /**
     * Constructor
     *
     * @param   string docroot document root
     */
    public function __construct($docroot) {
      $this->docroot= rtrim($docroot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Handle a single request
     *
     * @param   string method request method
     * @param   string query query string
     * @param   array<string, string> headers request headers
     * @param   string data post data
     * @param   peer.Socket socket
     */
    public function handleRequest($method, $query, array $headers, $data, Socket $socket) {
      $url= parse_url($query);
      $f= new File($this->docroot.strtr(
        preg_replace('#\.\./?#', '/', urldecode($url['path'])), 
        '/', 
        DIRECTORY_SEPARATOR
      ));
      try {
        $f->open(FILE_MODE_READ);
      } catch (FileNotFoundException $e) {
        $this->sendErrorMessage($socket, 404, 'Not found', $e->getMessage());
        return;
      } catch (IOException $e) {
        $this->sendErrorMessage($socket, 500, 'Internal server error', $e->getMessage());
        $f->close();
        return;
      }

      // Send OK header
      $this->sendHeader($socket, 200, 'OK', array(
        'Content-Type'    => MimeType::getByFileName($f->getFilename()),
        'Content-Length'  => $f->size(),
      ));
      
      // Send data in 8192 byte chunks
      while (!$f->eof()) {
        $socket->write($f->read(8192));
      }
      $f->close();
    }
  }
?>
