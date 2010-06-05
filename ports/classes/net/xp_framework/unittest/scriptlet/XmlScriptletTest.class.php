<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.XMLScriptlet',
    'xml.Stylesheet',
    'peer.URL'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.xml.XMLScriptlet
   */
  class XmlScriptletTest extends TestCase {

    /**
     * Set session path to current working directory
     *
     */
    public function setUp() {
      foreach (array('dom', 'xsl') as $ext) {
        if (!extension_loaded($ext)) {
          throw new PrerequisitesNotMetError($ext.' extension not loaded');
        }
      }

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
     * @return  scriptlet.xml.XMLScriptletRequest
     */
    protected function newRequest($method, URL $url) {
      $q= $url->getQuery('');
      $req= new XMLScriptletRequest();
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
     * Creates a new response object
     *
     * @return  scriptlet.xml.XMLScriptletResponse
     */
    protected function newResponse(Stylesheet $stylesheet) {
      $res= new XMLScriptletResponse();
      $res->setProcessor(new DOMXSLProcessor());
      $stylesheet->addParam('__state');
      $stylesheet->addParam('__page');
      $stylesheet->addParam('__lang');
      $stylesheet->addParam('__product');
      $stylesheet->addParam('__sess');
      $stylesheet->addParam('__query');
      $res->setStylesheet($stylesheet, XSLT_TREE);
      return $res;
    }

    /**
     * Test doGet() is invoked method
     *
     */
    #[@test]
    public function doGet() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate(create(new XslTemplate())->matching('/')
          ->withChild(create(new Node('html'))
            ->withChild(create(new Node('body'))
              ->withChild(new Node('xsl:value-of', NULL, array('select' => '/formresult/result')))
            )
          )
        )
      );
      
      $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->addFormResult(new Node("result", "GET"));
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>GET</body>\n</html>\n",
        $res->getContent()
      );
    }

    /**
     * Test creating a session performs a redirect onto the scriptlet URL
     * itself but with "psessionid=..." and the session's ID in the URL.
     *
     */
    #[@test]
    public function doCreate() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      
      $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
        public function needsSession($request) { return TRUE; }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_FOUND, $res->statusCode);
      
      // Check URL from Location: header contains the session ID
      with ($redirect= new URL(substr($res->headers[0], strlen('Location: ')))); {
        $this->assertEquals('http', $redirect->getScheme());
        $this->assertEquals('localhost', $redirect->getHost());
        $this->assertEquals(sprintf('/xml/psessionid=%s/static', session_id()), $redirect->getPath());
        $this->assertEquals(array(), $redirect->getParams(), $redirect->getURL());
      }
    }

    /**
     * Test writing to response with write() throws an exception
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function writeToResponseNotPermitted () {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= $this->newResponse(create(new Stylesheet())->withOutputMethod('xml'));
      
      $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->write("Hello");
        }
      }');
      $s->service($req, $res);
    }

    /**
     * Test writing to response with write() throws no exception if
     * processed flag is set to FALSE.
     *
     */
    #[@test]
    public function writeToResponsePermittedIfNotProcessed () {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= $this->newResponse(create(new Stylesheet())->withOutputMethod('xml'));
      
      $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
        public function doGet($request, $response) {
          $response->setProcessed(FALSE);
          $response->write("Hello");
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals('Hello', $res->getContent());
    }

    /**
     * Test doPost() is invoked method
     *
     */
    #[@test]
    public function doPost() {
      $req= $this->newRequest('POST', new URL('http://localhost/'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate(create(new XslTemplate())->matching('/')
          ->withChild(create(new Node('html'))
            ->withChild(create(new Node('body'))
              ->withChild(new Node('xsl:value-of', NULL, array('select' => '/formresult/result')))
            )
          )
        )
      );
      
      $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
        public function doPost($request, $response) {
          $response->addFormResult(new Node("result", "POST"));
        }
      }');
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>POST</body>\n</html>\n",
        $res->getContent()
      );
    }
    
    /**
     * Creates a template that will dump all special "__"-parameters
     *
     * @return  xml.XslTemplate
     */
    protected function dumpParamsTemplate() {
      return create(new XslTemplate())->matching('/')
        ->withChild(create(new Node('html'))
          ->withChild(create(new Node('body'))
            ->withChild(new Node('xsl:value-of', NULL, array('select' => 'concat(
              "state=",   $__state, ", ",
              "page=",    $__page, ", ",
              "lang=",    $__lang, ", ",
              "product=", $__product, ", ",
              "sess=",    $__sess, ", ",
              "query=",   $__query
            )')))
          )
        )
      ;
    }

    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function requestParametersAppearInFormresult() {
      $req= $this->newRequest('GET', new URL('http://localhost/?a=b&b=c'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate(create(new XslTemplate())->matching('/')
          ->withChild(create(new Node('html'))
            ->withChild(create(new Node('body'))
              ->withChild(new Node('xsl:copy-of', NULL, array('select' => '/formresult/formvalues')))
            )
          )
        )
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        '<html>'."\n".
        '  <body>'."\n".
        '    <formvalues xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n".
        '      <param name="a" xsi:type="xsd:string">b</param>'."\n".
        '      <param name="b" xsi:type="xsd:string">c</param>'."\n".
        '    </formvalues>'."\n".
        '  </body>'."\n".
        '</html>'."\n",
        $res->getContent()
      );
    }

    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function requestArrayParametersAppearInFormresult() {
      $req= $this->newRequest('GET', new URL('http://localhost/?a[]=b&a[]=c'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate(create(new XslTemplate())->matching('/')
          ->withChild(create(new Node('html'))
            ->withChild(create(new Node('body'))
              ->withChild(new Node('xsl:copy-of', NULL, array('select' => '/formresult/formvalues')))
            )
          )
        )
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        '<html>'."\n".
        '  <body>'."\n".
        '    <formvalues xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n".
        '      <param name="a" xsi:type="xsd:string">b</param>'."\n".
        '      <param name="a" xsi:type="xsd:string">c</param>'."\n".
        '    </formvalues>'."\n".
        '  </body>'."\n".
        '</html>'."\n",
        $res->getContent()
      );
    }
    
    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function defaultParameters() {
      $req= $this->newRequest('GET', new URL('http://localhost/'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate($this->dumpParamsTemplate())
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>state=static, page=home, lang=en_US, product=, sess=, query=</body>\n</html>\n",
        $res->getContent()
      );
    }

    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function homeState() {
      $req= $this->newRequest('GET', new URL('http://localhost/xml/home'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate($this->dumpParamsTemplate())
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>state=home, page=home, lang=en_US, product=, sess=, query=</body>\n</html>\n",
        $res->getContent()
      );
    }

    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function productAndLanguage() {
      $req= $this->newRequest('GET', new URL('http://localhost/xml/public.de_DE/home'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate($this->dumpParamsTemplate())
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>state=home, page=home, lang=de_DE, product=public, sess=, query=</body>\n</html>\n",
        $res->getContent()
      );
    }

    /**
     * Test parameters passed to XSL
     *
     */
    #[@test]
    public function query() {
      $req= $this->newRequest('GET', new URL('http://localhost/?a=b'));
      $res= $this->newResponse(create(new Stylesheet())
        ->withOutputMethod('xml')
        ->withTemplate($this->dumpParamsTemplate())
      );
      
      $s= new XMLScriptlet();
      $s->service($req, $res);
      $this->assertEquals(HttpConstants::STATUS_OK, $res->statusCode);
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
        "<html>\n  <body>state=static, page=home, lang=en_US, product=, sess=, query=a=b</body>\n</html>\n",
        $res->getContent()
      );
    }
  }
?>
