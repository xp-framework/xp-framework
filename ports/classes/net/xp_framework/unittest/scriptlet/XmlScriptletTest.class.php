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
      $res= $this->newResponse(create(new Stylesheet())->withOutputMethod('xml')
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
  }
?>
