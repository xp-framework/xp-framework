<?php namespace xp\scriptlet;

use peer\URL;
use peer\Socket;
use scriptlet\ScriptletException;

/**
 * Scriptlet handler
 */
class ScriptletHandler extends AbstractUrlHandler {
  protected $scriptlet;
  protected $request;
  protected $response;
  protected $env;
  protected $serverName;

  /**
   * Constructor
   *
   * @param   string name
   * @param   string[] args
   * @param   [:string] env
   */
  public function __construct($name, $args, $env= array()) {
    $class= \lang\XPClass::forName($name);
    if ($class->hasConstructor()) {
      $this->scriptlet= $class->getConstructor()->newInstance((array)$args);
    } else {
      $this->scriptlet= $class->newInstance();
    }
    $this->scriptlet->init();
    $this->request= $class->getMethod('_request')->setAccessible(true);
    $this->response= $class->getMethod('_response')->setAccessible(true);
    $this->env= $env;
    $this->serverName= \lang\System::getProperty('host.name');
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
    $url= new URL('http://'.(isset($headers['Host']) ? $headers['Host'] : $this->serverName).$query);
    $request= $this->request->invoke($this->scriptlet, array());
    $response= $this->response->invoke($this->scriptlet, array());

    // Fill request
    $request->method= $method;
    $request->env= $this->env;
    $request->env['SERVER_PROTOCOL']= 'HTTP/1.1';
    $request->env['REQUEST_URI']= $query;
    $request->env['QUERY_STRING']= substr($query, strpos($query, '?')+ 1);
    $request->env['HTTP_HOST']= $url->getHost();
    if ('https' === $url->getScheme()) {
      $request->env['HTTPS']= 'on';
    }
    if (isset($headers['Authorization'])) {
      if (0 === strncmp('Basic', $headers['Authorization'], 5)) {
        $credentials= explode(':', base64_decode(substr($headers['Authorization'], 6)));
        $request->env['PHP_AUTH_USER']= $credentials[0];
        $request->env['PHP_AUTH_PW']= $credentials[1];
      }
    }
    $_COOKIE= array();
    if (isset($headers['Cookie'])) {
      foreach (explode(';', $headers['Cookie']) as $cookie) {
        sscanf(trim($cookie), "%[^=]=%[^\r]", $name, $value);
        $_COOKIE[$name]= $value;
      }
    }
    $request->setHeaders($headers);

    try {
      $this->scriptlet->service($request, $response);
    } catch (ScriptletException $e) {
      $e->printStackTrace();
      $this->sendErrorMessage($socket, $e->getStatus(), $e->getClassName(), $e->getMessage());
      return;
    }

    $h= array();
    foreach ($response->headers as $header) {
      list($name, $value)= explode(': ', $header, 2);
      if (isset($h[$name])) {
        $h[$name]= array($h[$name], $value);
      } else {
        $h[$name]= $value;
      }
    }
    $this->sendHeader($socket, $response->statusCode, '', $h);
    $socket->write($response->getContent());
    return $response->statusCode;
  }

  /**
   * Returns a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'<'.$this->scriptlet->getClassName().'>';
  }
}
