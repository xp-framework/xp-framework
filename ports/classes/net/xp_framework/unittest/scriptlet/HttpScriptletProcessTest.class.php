<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptlet',
    'peer.URL'
  );

  /**
   * TestCase for the deprecated HttpScriptlet::process() method
   *
   * @see      xp://scriptlet.HttpScriptlet
   */
  class HttpScriptletProcessTest extends TestCase {
  
    static function __static() {
      if (!function_exists('getallheaders')) {
        function getallheaders() { return array(); }
      }
    }
    
    /**
     * Set session path to current working directory
     *
     */
    public function setUp() {
      session_save_path(getcwd());
    }

    /**
     * Destroy session and cleanup file
     *
     */
    public function tearDown() {
      if (session_id()) {
        session_write_close();
        unlink(session_save_path().DIRECTORY_SEPARATOR.'sess_'.session_id());
        session_id(NULL);
      }
    }
    
    /**
     * Creates a new request object. Uses the system environment and global
     * variables to put necessary parameters into place.
     *
     * @param   string method
     * @param   peer.URL url
     */
    protected function newRequest($method, URL $url) {
      $q= $url->getQuery();
      putenv('REQUEST_METHOD='.$method);
      putenv('SERVER_PROTOCOL=HTTP/1.1');
      putenv('HTTP_HOST='.$url->getHost());
      putenv('REQUEST_URI='.$url->getPath('/').($q ? '?'.$q : '')."\0");
      putenv('QUERY_STRING='.$q);
      if ('https' === $url->getScheme()) { 
        putenv('HTTPS=on');
      }
      $_REQUEST= $url->getParams();
    }
  
    /**
     * Test GET method
     *
     */
    #[@test]
    public function getSupported() {
      $this->newRequest('GET', new URL('http://localhost/'));
      
      $s= new HttpScriptlet();
      $s->process();
    }

    /**
     * Test HEAD method
     *
     */
    #[@test]
    public function headSupported() {
      $this->newRequest('HEAD', new URL('http://localhost/'));
      
      $s= new HttpScriptlet();
      $s->process();
    }

    /**
     * Test POST method
     *
     */
    #[@test]
    public function postSupported() {
      $this->newRequest('POST', new URL('http://localhost/'));
      
      $s= new HttpScriptlet();
      $s->process();
    }

    /**
     * Test TRACE method
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function traceUnSupported() {
      $this->newRequest('TRACE', new URL('http://localhost/'));
      
      $s= new HttpScriptlet();
      $s->process();
    }

    /**
     * Test doGet() is invoked method
     *
     */
    #[@test]
    public function doGet() {
      $this->newRequest('GET', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->write("Hello GET");
        }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_OK, $response->statusCode);
      $this->assertEquals('Hello GET', $response->getContent());
    }

    /**
     * Test doHead() is invoked method
     *
     */
    #[@test]
    public function doHead() {
      $this->newRequest('HEAD', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doHead($request, $response) {
          $response->write("Hello HEAD");
        }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_OK, $response->statusCode);
      $this->assertEquals('Hello HEAD', $response->getContent());
    }

    /**
     * Test doPost() is invoked method
     *
     */
    #[@test]
    public function doPost() {
      $this->newRequest('POST', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doPost($request, $response) {
          $response->write("Hello POST");
        }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_OK, $response->statusCode);
      $this->assertEquals('Hello POST', $response->getContent());
    }

    /**
     * Test any exceptions thrown from do*() methods are wrapped inside
     * a scriptlet.HttpScriptletException
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function exceptionInDoWrapped() {
      $this->newRequest('GET', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          throw new IllegalArgumentException("TEST");
        }
      }');
      $response= $s->process();
    }

    /**
     * Test sendRedirect() sends a 302 Found header and the redirect
     * URL inside the Location: header
     *
     */
    #[@test]
    public function sendRedirect() {
      $this->newRequest('GET', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->sendRedirect("http://localhost/home");
        }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_FOUND, $response->statusCode);
      $this->assertEquals('Location: http://localhost/home', $response->headers[0]);
    }

    /**
     * Test creating a session performs a redirect onto the scriptlet URL
     * itself but with "?psessionid=..." and the session's ID appended.
     *
     */
    #[@test]
    public function createSession() {
      $this->newRequest('GET', new URL('http://localhost/'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_FOUND, $response->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($response->headers[0], strlen('Location: ')))); {
        $this->assertEquals('http', $redirect->getScheme());
        $this->assertEquals('localhost', $redirect->getHost());
        $this->assertEquals('/', $redirect->getPath());
        $this->assertEquals(session_id(), $redirect->getParam('psessionid', ''), $redirect->getURL());
      }
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test]
    public function invalidSessionCreatesNewSession() {
      $this->newRequest('GET', new URL('http://localhost/?psessionid=INVALID'));
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_FOUND, $response->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($response->headers[0], strlen('Location: ')))); {
        $this->assertEquals('http', $redirect->getScheme());
        $this->assertEquals('localhost', $redirect->getHost());
        $this->assertEquals('/', $redirect->getPath());
        $this->assertEquals(session_id(), $redirect->getParam('psessionid'));
      }
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test, @expect('scriptlet.HttpSessionInvalidException')]
    public function invalidSession() {
      $this->newRequest('GET', new URL('http://localhost/?psessionid=INVALID'));

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
        public function handleInvalidSession($request, $response) { return FALSE; } 
      }');
      $s->process();
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test, @expect('scriptlet.HttpSessionInvalidException')]
    public function sessionInitializationError() {
      $this->newRequest('GET', new URL('http://localhost/?psessionid=MALFORMED'));

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
        public function handleSessionInitialization($request) {
          if (!preg_match("/^a-f0-9$/", $request->getSessionId())) { 
            throw new IllegalArgumentException("Invalid characters in session id");
          }
          parent::handleSessionInitialization($request);
        } 
      }');
      $s->process();
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test]
    public function handleSessionInitializationError() {
      $this->newRequest('GET', new URL('http://localhost/?psessionid=MALFORMED'));

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
        public function handleSessionInitialization($request) {
          if (!preg_match("/^a-f0-9$/", $request->getSessionId())) { 
            throw new IllegalArgumentException("Invalid characters in session id");
          }
          parent::handleSessionInitialization($request);
        }
        public function handleSessionInitializationError($request, $response) {
          $request->getURL()->addParam("relogin", 1);
          return $request->session->initialize(NULL);
        } 
      }');
      $response= $s->process();
      $this->assertEquals(HttpConstants::STATUS_FOUND, $response->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($response->headers[0], strlen('Location: ')))); {
        $this->assertEquals('http', $redirect->getScheme());
        $this->assertEquals('localhost', $redirect->getHost());
        $this->assertEquals('/', $redirect->getPath());
        $this->assertEquals(session_id(), $redirect->getParam('psessionid'));
        $this->assertEquals('1', $redirect->getParam('relogin'));
      }
    }
  }
?>
