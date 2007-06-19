<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for all URL handlers
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractUrlHandler extends Object {

    /**
     * Send a HTTP header message
     *
     * @param   peer.Socket socket
     * @param   int sc the status code
     * @param   string message status message
     * @param   array<string, string> headers
     */
    protected function sendHeader(Socket $socket, $sc, $message, array $headers) {
      $socket->write('HTTP/1.1 '.$sc.' '.$message."\r\n");
      foreach ($headers as $key => $value) {
        $socket->write($key.': '.$value."\r\n");
      }
      $socket->write("\r\n");
    }
    
    /**
     * Send a HTTP error message
     *
     * @param   peer.Socket socket
     * @param   int sc the status code
     * @param   string message status message
     * @param   string body the body
     */
    protected function sendErrorMessage(Socket $socket, $sc, $message, $body) {
      $this->sendHeader($socket, $sc, $message, array(
        'Content-Type'    => 'text/html',
        'Content-Length'  => strlen($body),
      ));
      $socket->write($body);
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
    public abstract function handleRequest($method, $query, array $headers, $data, Socket $socket);
  }
?>
