<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.Input', 'webservices.rest.srv.Upload');

  /**
   * Represents multipart/form-data
   *
   * @see   php://is_uploaded_file
   */
  class Uploads extends webservices·rest·srv·Input {

    /**
     * Creates a new Input instance
     *
     * @param scriptlet.Request $request
     */
    public function __construct(scriptlet·Request $request) {
      parent::__construct($request);
      if ('POST' !== $request->getMethod()) {

        // PHP doesn't parse these, we'll need to that ourselves
        $contentType= $request->getContentType();
        $boundary= substr($contentType, strpos($contentType, 'boundary=') + 9);

        $this->source= 'input:'.$boundary;
        $this->reader= function($request, $name) use($boundary) {
          $stream= $request->getInputStream();
          $s= strlen($boundary) + 2;

          // Read headers
          do {
            $headers= '';
            while ($stream->available() && FALSE === ($p= strpos($headers, "\r\n\r\n"))) {
              $headers.= $stream->read();
            }
            $body= substr($headers, $p);
            $headers= substr($headers, $s, $p - 4 - $s);

            // XXX TODO finish implementation
            // \util\cmd\Console::writeLine('HEADERS : ', new \lang\types\Bytes($headers));

          } while (0);
        };
      } else {
        $this->source= 'files';
        $this->reader= function($request, $name) {
          $r= array();
          $params= $request->getParam($name);
          foreach (isset($params['tmp_name']) ? array($params) : $params as $param) {
            if (!is_uploaded_file($param['tmp_name'])) {
              throw new IllegalArgumentException('Parameter "'.$name.'" is not an uploaded file!');
            }
            $r[]= new Upload($param);
          }
          return $r;
        };
      }
    }

    /**
     * Returns all uploads by a given name
     *
     * @param  string $name
     * @return webservices.rest.srv.Upload[]
     */
    public function eachNamed($name) {
      return call_user_func($this->reader, $this->request, $name);
    }

    public function toString() {
      return $this->getClassName().'<'.$this->source.'>';
    }
  }
?>
