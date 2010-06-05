<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptlet',
    'scriptlet.RequestAuthenticator',
    'peer.URL'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.HttpScriptlet
   */
  class HttpScriptletTest extends TestCase {
    protected static $helloScriptlet= NULL;
    
    static function __static() {
      self::$helloScriptlet= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doPost($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doHead($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doTrace($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doConnect($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doOptions($request, $response) {
          $response->write("Hello ".$request->method);
        }
        public function doDelete($request, $response) {
          $response->write("Hello ".$request->method);
        }
      }');
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
     * Helper method
     *
     * @param   string method
     * @param   string expect
     */
    protected function assertHandlerForMethodTriggered($method) {
      $req= $this->newRequest($method, new URL('http://localhost/'));
      $res= new HttpScriptletResponse();

      self::$helloScriptlet->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals('Hello '.$method, $res->getContent());
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
     * Test illegal HTTP verb is not supported
     *
     */
    #[@test, @expect(class= 'scriptlet.HttpScriptletException', withMessage= 'Unknown HTTP method: "LOOK"')]
    public function illegalHttpVerb() {
      $req= $this->newRequest('LOOK', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= new HttpScriptlet();
      $s->service($req, $res);
    }

    /**
     * Test doGet() method is invoked
     *
     */
    #[@test]
    public function doGet() {
      $this->assertHandlerForMethodTriggered('GET');
    }

    /**
     * Test doHead() method is invoked
     *
     */
    #[@test]
    public function doHead() {
      $this->assertHandlerForMethodTriggered('HEAD');
    }

    /**
     * Test doPost() method is invoked
     *
     */
    #[@test]
    public function doPost() {
      $this->assertHandlerForMethodTriggered('POST');
    }

    /**
     * Test doDelete() method is invoked
     *
     */
    #[@test]
    public function doDelete() {
      $this->assertHandlerForMethodTriggered('DELETE');
    }

    /**
     * Test doOptions() method is invoked
     *
     */
    #[@test]
    public function doOptions() {
      $this->assertHandlerForMethodTriggered('OPTIONS');
    }

    /**
     * Test doTrace() method is invoked
     *
     */
    #[@test]
    public function doTrace() {
      $this->assertHandlerForMethodTriggered('TRACE');
    }

    /**
     * Test doConnect() method is invoked
     *
     */
    #[@test]
    public function doConnect() {
      $this->assertHandlerForMethodTriggered('CONNECT');
    }

    /**
     * Test non-implemented method triggers MethodNotImplementedException
     *
     */
    #[@test, @expect(class= 'scriptlet.HttpScriptletException', withMessage= 'HTTP method "DELETE" not supported')]
    public function requestedMethodNotImplemented() {
      $req= $this->newRequest('DELETE', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          throw new IllegalStateException("Should not be reached");
        }
      }');
      $s->service($req, $res);
    }

    /**
     * Test any exceptions thrown from do*() methods are wrapped inside
     * a scriptlet.HttpScriptletException
     *
     */
    #[@test, @expect(class= 'scriptlet.HttpScriptletException', withMessage= 'Request processing failed [doGet]: TEST')]
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
     * Test any exceptions thrown from do*() methods are wrapped inside
     * a scriptlet.HttpScriptletException
     *
     */
    #[@test, @expect(class= 'scriptlet.HttpScriptletException', withMessage= 'TEST')]
    public function scriptletExceptionInDo() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function doGet($request, $response) {
          throw new HttpScriptletException("TEST");
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

    /**
     * Test authenticator that always throws exceptions
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function unconditionalDenyAuthenticator() {
      $req= $this->newRequest('GET', new URL('http://localhost/members/profile'));
      $res= new HttpScriptletResponse();

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function getAuthenticator($request) {
          if (!strstr($request->getURL()->getPath(), "/members")) return NULL;
          
          return newinstance(\'scriptlet.RequestAuthenticator\', array(), \'{
            public function authenticate($request, $response, $context) {
              throw new IllegalAccessException("Valid user required");
            }
          }\');
        }
      }');
      $s->service($req, $res);
    }

    /**
     * Test authenticator that redirects
     *
     */
    #[@test]
    public function redirectingAuthenticator() {
      $req= $this->newRequest('GET', new URL('http://localhost/members/profile'));
      $res= new HttpScriptletResponse();

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function getAuthenticator($request) {
          if (!strstr($request->getURL()->getPath(), "/members")) return NULL;
          
          return newinstance(\'scriptlet.RequestAuthenticator\', array(), \'{
            public function authenticate($request, $response, $context) {
              $response->sendRedirect("http://localhost/login");
              return FALSE;
            }
          }\');
        }
        
        public function doGet($request, $response) {
          throw new IllegalAccessException("Valid user required");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      $this->assertEquals('Location: http://localhost/login', $res->headers[0]);
    }

    /**
     * Test authenticator that always returns TRUE
     *
     */
    #[@test]
    public function unconditionalAllowAuthenticator() {
      $req= $this->newRequest('GET', new URL('http://localhost/members/profile'));
      $res= new HttpScriptletResponse();

      $s= newinstance('scriptlet.HttpScriptlet', array(), '{
        public function getAuthenticator($request) {
          if (!strstr($request->getURL()->getPath(), "/members")) return NULL;
          
          return newinstance(\'scriptlet.RequestAuthenticator\', array(), \'{
            public function authenticate($request, $response, $context) {
              return TRUE;
            }
          }\');
        }
        
        public function doGet($request, $response) {
          $response->write("Welcome!");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals('Welcome!', $res->getContent());
    }
  }
?>
