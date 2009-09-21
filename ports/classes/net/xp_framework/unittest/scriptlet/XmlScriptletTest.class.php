<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.XMLScriptlet',
    'scriptlet.RequestAuthenticator',
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
     * Creates a new request object
     *
     * @param   string method
     * @param   peer.URL url
     * @return  scriptlet.XMLScriptletRequest
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
     * @return  scriptlet.XMLScriptletResponse
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
      
      $s= newinstance('scriptlet.XMLScriptlet', array(), '{
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
      
      $s= newinstance('scriptlet.XMLScriptlet', array(), '{
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
