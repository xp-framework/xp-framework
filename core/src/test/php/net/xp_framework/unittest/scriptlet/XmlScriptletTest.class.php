<?php namespace net\xp_framework\unittest\scriptlet;

use scriptlet\xml\XMLScriptlet;
use xml\Stylesheet;
use xml\DomXSLProcessor;

/**
 * TestCase
 *
 * @see      xp://scriptlet.xml.XMLScriptlet
 */
class XmlScriptletTest extends ScriptletTestCase {

  /**
   * Verify dom and xsl extensions are loaded
   *
   */
  public function setUp() {
    foreach (array('dom', 'xsl') as $ext) {
      if (!extension_loaded($ext)) {
        throw new \unittest\PrerequisitesNotMetError($ext.' extension not loaded');
      }
    }
  }

  /**
   * Creates a new request object
   *
   * @param   string method
   * @param   peer.URL url
   * @return  scriptlet.xml.XMLScriptletRequest
   */
  protected function newRequest($method, \peer\URL $url) {
    $q= $url->getQuery('');
    $req= new \scriptlet\xml\XMLScriptletRequest();
    $req->method= $method;
    $req->env['SERVER_PROTOCOL']= 'HTTP/1.1';
    $req->env['REQUEST_URI']= $url->getPath('/').($q ? '?'.$q : '');
    $req->env['QUERY_STRING']= $q;
    $req->env['HTTP_HOST']= $url->getHost();
    $req->env['LANGUAGE']= 'en_US';
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
    $res= new \scriptlet\xml\XMLScriptletResponse();
    $res->setProcessor(new DomXSLProcessor());
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate(create(new \xml\XslTemplate())->matching('/')
        ->withChild(create(new \xml\Node('html'))
          ->withChild(create(new \xml\Node('body'))
            ->withChild(new \xml\Node('xsl:value-of', null, array('select' => '/formresult/result')))
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
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/'));
    $res= new \scriptlet\HttpScriptletResponse();
    
    $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
      public function needsSession($request) { return TRUE; }
    }');
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_FOUND, $res->statusCode);
    
    // Check URL from Location: header contains the session ID
    with ($redirect= new \peer\URL(substr($res->headers[0], strlen('Location: ')))); {
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
  #[@test, @expect('scriptlet.ScriptletException')]
  public function writeToResponseNotPermitted () {
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/'));
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/'));
    $res= $this->newResponse(create(new Stylesheet())->withOutputMethod('xml'));
    
    $s= newinstance('scriptlet.xml.XMLScriptlet', array(), '{
      public function doGet($request, $response) {
        $response->setProcessed(FALSE);
        $response->write("Hello");
      }
    }');
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
    $this->assertEquals('Hello', $res->getContent());
  }

  /**
   * Test doPost() is invoked method
   *
   */
  #[@test]
  public function doPost() {
    $req= $this->newRequest('POST', new \peer\URL('http://localhost/'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate(create(new \xml\XslTemplate())->matching('/')
        ->withChild(create(new \xml\Node('html'))
          ->withChild(create(new \xml\Node('body'))
            ->withChild(new \xml\Node('xsl:value-of', null, array('select' => '/formresult/result')))
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
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    return create(new \xml\XslTemplate())->matching('/')
      ->withChild(create(new \xml\Node('html'))
        ->withChild(create(new \xml\Node('body'))
          ->withChild(new \xml\Node('xsl:value-of', null, array('select' => 'concat(
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/?a=b&b=c'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate(create(new \xml\XslTemplate())->matching('/')
        ->withChild(create(new \xml\Node('html'))
          ->withChild(create(new \xml\Node('body'))
            ->withChild(new \xml\Node('xsl:copy-of', null, array('select' => '/formresult/formvalues')))
          )
        )
      )
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/?a[]=b&a[]=c'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate(create(new \xml\XslTemplate())->matching('/')
        ->withChild(create(new \xml\Node('html'))
          ->withChild(create(new \xml\Node('body'))
            ->withChild(new \xml\Node('xsl:copy-of', null, array('select' => '/formresult/formvalues')))
          )
        )
      )
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate($this->dumpParamsTemplate())
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/xml/home'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate($this->dumpParamsTemplate())
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/xml/public.de_DE/home'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate($this->dumpParamsTemplate())
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
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
    $req= $this->newRequest('GET', new \peer\URL('http://localhost/?a=b'));
    $res= $this->newResponse(create(new Stylesheet())
      ->withEncoding('iso-8859-1')
      ->withOutputMethod('xml')
      ->withTemplate($this->dumpParamsTemplate())
    );
    
    $s= new XMLScriptlet();
    $s->service($req, $res);
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $res->statusCode);
    $this->assertEquals(
      '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".
      "<html>\n  <body>state=static, page=home, lang=en_US, product=, sess=, query=a=b</body>\n</html>\n",
      $res->getContent()
    );
  }
}
