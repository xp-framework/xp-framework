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
   * TestCase
   *
   * @see      xp://scriptlet.HttpScriptlet
   */
  class HttpScriptletTest extends TestCase {
  
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
     * Creates a new request object
     *
     * @param   string method
     * @param   peer.URL url
     * @return  scriptlet.HttpScriptletRequest
     */
    protected function newRequest($method, URL $url) {
      $q= $url->getQuery('');
      $req= new HttpScriptletRequest();
      $req->method= $method;
      $req->env['SERVER_PROTOCOL']= 'HTTP/1.1';
      $req->env['REQUEST_URI']= $url->getPath('/').($q ? '?'.$q : '');
      $req->env['QUERY_STRING']= $q;
      $req->env['HTTP_HOST']= $url->getHost();
      if ('https' === $url->getScheme()) { 
        $req->env['HTTPS']= 'on';
      }
      $req->setHeaders(array());
      $req->setParams($url->getParams());
      return $req;
    }
  
    /**
     * Test GET method
     *
     */
    #[@test]
    public function getSupported() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= new HttpScriptlet($req, $res);
      $s->service($req, $res);
    }

    /**
     * Test HEAD method
     *
     */
    #[@test]
    public function headSupported() {
      $req= $this->newRequest('HEAD', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= new HttpScriptlet();
      $s->service($req, $res);
    }

    /**
     * Test POST method
     *
     */
    #[@test]
    public function postSupported() {
      $req= $this->newRequest('POST', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= new HttpScriptlet();
      $s->service($req, $res);
    }

    /**
     * Test TRACE method
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function traceUnSupported() {
      $req= $this->newRequest('TRACE', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= new HttpScriptlet();
      $s->service($req, $res);
    }

    /**
     * Test doGet() is invoked method
     *
     */
    #[@test]
    public function doGet() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->write("Hello GET");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals('Hello GET', $res->getContent());
    }

    /**
     * Test doHead() is invoked method
     *
     */
    #[@test]
    public function doHead() {
      $req= $this->newRequest('HEAD', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doHead($request, $response) {
          $response->write("Hello HEAD");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals('Hello HEAD', $res->getContent());
    }

    /**
     * Test doPost() is invoked method
     *
     */
    #[@test]
    public function doPost() {
      $req= $this->newRequest('POST', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doPost($request, $response) {
          $response->write("Hello POST");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals('Hello POST', $res->getContent());
    }

    /**
     * Test any exceptions thrown from do*() methods are wrapped inside
     * a scriptlet.HttpScriptletException
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function exceptionInDoWrapped() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          throw new IllegalArgumentException("TEST");
        }
      }');
      $s->service($req, $res);
    }

    /**
     * Test sendRedirect() sends a 302 Found header and the redirect
     * URL inside the Location: header
     *
     */
    #[@test]
    public function sendRedirect() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->sendRedirect("http://localhost/home");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      $this->assertEquals('Location: http://localhost/home', $res->headers[0]);
    }

    /**
     * Test creating a session performs a redirect onto the scriptlet URL
     * itself but with "?psessionid=..." and the session's ID appended.
     *
     */
    #[@test]
    public function createSession() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($res->headers[0], strlen('Location: ')))); {
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
      $req= $this->newRequest('GET', new URL('http://localhost/?psessionid=INVALID'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($res->headers[0], strlen('Location: ')))); {
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
      $req= $this->newRequest('GET', new URL('http://localhost/?psessionid=INVALID'));
      $res= new HttpScriptletResponse();

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
        public function handleInvalidSession($request, $response) { return FALSE; } 
      }');
      $s->service($req, $res);
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test, @expect('scriptlet.HttpSessionInvalidException')]
    public function sessionInitializationError() {
      $req= $this->newRequest('GET', new URL('http://localhost/?psessionid=MALFORMED'));
      $res= new HttpScriptletResponse();

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
        public function handleSessionInitialization($request) {
          if (!preg_match("/^a-f0-9$/", $request->getSessionId())) { 
            throw new IllegalArgumentException("Invalid characters in session id");
          }
          parent::handleSessionInitialization($request);
        } 
      }');
      $s->service($req, $res);
    }

    /**
     * Test accessing a scriptlet with an invalid session
     *
     */
    #[@test]
    public function handleSessionInitializationError() {
      $req= $this->newRequest('GET', new URL('http://localhost/?psessionid=MALFORMED'));
      $res= new HttpScriptletResponse();

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
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($res->headers[0], strlen('Location: ')))); {
        $this->assertEquals('http', $redirect->getScheme());
        $this->assertEquals('localhost', $redirect->getHost());
        $this->assertEquals('/', $redirect->getPath());
        $this->assertEquals(session_id(), $redirect->getParam('psessionid'));
        $this->assertEquals('1', $redirect->getParam('relogin'));
      }
    }
  }
?>
