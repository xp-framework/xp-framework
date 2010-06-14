<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.scriptlet';
  
  uses(
    'unittest.TestCase',
    'xp.scriptlet.Runner',
    'xml.Stylesheet',
    'xml.Node',
    'util.log.Traceable',
    'util.log.BufferedAppender',
    'scriptlet.HttpScriptlet',
    'scriptlet.xml.XMLScriptlet'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.scriptlet.Runner
   */
  class net暖p_framework暉nittest新criptlet愛unnerTest extends TestCase {
    protected static $welcomeScriptlet= NULL;
    protected static $errorScriptlet= NULL;
    protected static $debugScriptlet= NULL;
    protected static $xmlScriptlet= NULL;
    
    static function __static() {
      self::$errorScriptlet= ClassLoader::defineClass('ErrorScriptlet', 'scriptlet.HttpScriptlet', array('util.log.Traceable'), '{
        protected function _request() {
          $req= parent::_request();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/error";
          $req->env["HTTP_HOST"]= "localhost";
          return $req;
        }
        
        public function setTrace($cat) {
          $cat->debug("Injected", $cat->getClassName());
        }
        
        protected function _setupRequest($request) {
          // Intentionally empty
        }
        
        public function doGet($request, $response) {
          throw new IllegalAccessException("No shoes, no shorts, no service");
        }
      }');
      self::$welcomeScriptlet= ClassLoader::defineClass('WelcomeScriptlet', 'scriptlet.HttpScriptlet', array(), '{
        protected function _request() {
          $req= parent::_request();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/welcome";
          $req->env["HTTP_HOST"]= "localhost";
          return $req;
        }
        
        protected function _setupRequest($request) {
          // Intentionally empty
        }
        
        public function doGet($request, $response) {
          $response->write("<h1>Welcome, we are open</h1>");
        }
      }');
      self::$xmlScriptlet= ClassLoader::defineClass('XmlScriptletImpl', 'scriptlet.xml.XMLScriptlet', array(), '{
        protected function _request() {
          $req= parent::_request();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/welcome";
          $req->env["HTTP_HOST"]= "localhost";
          return $req;
        }

        protected function _response() {
          $res= parent::_response();
          $stylesheet= create(new Stylesheet())
            ->withOutputMethod("xml")
            ->withTemplate(create(new XslTemplate())->matching("/")
              ->withChild(create(new Node("h1"))
                ->withChild(new Node("xsl:value-of", NULL, array("select" => "/formresult/result")))
              )
            )
          ;
          $res->setStylesheet($stylesheet, XSLT_TREE);
          return $res;
        }
        
        protected function _setupRequest($request) {
          // Intentionally empty
        }
        
        public function doGet($request, $response) {
          $response->addFormresult(new Node("result", "Welcome, we are open"));
        }
      }');
      self::$debugScriptlet= ClassLoader::defineClass('DebugScriptlet', 'scriptlet.HttpScriptlet', array(), '{
        protected $title, $date;

        public function __construct($title, $date) {
          $this->title= $title;
          $this->date= $date;
        }
        
        protected function _request() {
          $req= parent::_request();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/debug";
          $req->env["HTTP_HOST"]= "localhost";
          return $req;
        }
        
        protected function _setupRequest($request) {
          // Intentionally empty
        }
        
        public function doGet($request, $response) {
          $response->write("<h1>".$this->title." @ ".$this->date."</h1>");

          $response->write("<ul>");
          $response->write("  <li>ENV.DOMAIN = ".$request->getEnvValue("DOMAIN")."</li>");
          $response->write("  <li>ENV.ADMINS = ".$request->getEnvValue("ADMINS")."</li>");
          $response->write("</ul>");

          $config= PropertyManager::getInstance()->getProperties("debug")->getFileName();
          $response->write("<h2>".strtr($config, DIRECTORY_SEPARATOR, "/")."</h2>");
        }
      }');
    }

    /**
     * Verifies configure() method with all possible settings
     *
     */
    #[@test]
    public function configure() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.service', '/service');

        $p->writeSection('app::service');
        $p->writeString('app::service', 'class', 'ServiceScriptlet');
        $p->writeString('app::service', 'prop-base', '{WEBROOT}/etc/{PROFILE}');
        $p->writeString('app::service', 'init-envs', 'ROLE:admin|CLUSTER:a');
        $p->writeString('app::service', 'init-params', 'a|b');

        $p->writeSection('app::service@dev');
        $p->writeString('app::service@dev', 'debug', 'STACKTRACE|ERRORS');

        $r= new xp新criptlet愛unner('/htdocs', 'dev');
        $r->configure($p);

        $this->assertEquals(
          create(new WebApplication('service'))
            ->withConfig('/htdocs/etc/dev')
            ->withScriptlet('ServiceScriptlet')
            ->withEnvironment(array('ROLE' => 'admin', 'CLUSTER' => 'a'))
            ->withDebug(WebDebug::STACKTRACE | WebDebug::ERRORS)
            ->withArguments(array('a', 'b'))
          ,
          $r->applicationAt('/service')
        );
      }
    }

    /**
     * Verifies unknown debug flag in configuration raises an exception
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'No flag named WebDebug::UNKNOWN')]
    public function configureWithUnknownDebugFlag() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.service', '/service');
        $p->writeSection('app::service');
        $p->writeString('app::service', 'debug', 'UNKNOWN');
        create(new xp新criptlet愛unner('/htdocs'))->configure($p);
      }
    }

    /**
     * Verifies that empty configured mappings produce correct result
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: "app" section missing or broken')]
    public function emptyMappings() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        create(new xp新criptlet愛unner('/htdocs'))->configure($p);
      }
    }

    /**
     * Verifies that empty configured mappings produce correct result
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: "app" section missing or broken')]
    public function appSectionWithoutValidMappings() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'not.a.mapping', 1);
        create(new xp新criptlet愛unner('/htdocs'))->configure($p);
      }
    }

    /**
     * Verifies that old-style configured mappings produce correct result
     *
     */
    #[@test]
    public function oldStyleMappings() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'mappings', '/service:service|/:global');

        $p->writeSection('app::service');
        $p->writeSection('app::global');

        $r= new xp新criptlet愛unner('/htdocs');
        $r->configure($p);

        $this->assertEquals(
          array(
            '/service' => create(new WebApplication('service'))->withConfig('/htdocs/etc'), 
            '/'        => create(new WebApplication('global'))->withConfig('/htdocs/etc')
          ),
          $r->mappedApplications()
        );
      }
    }

    /**
     * Verifies that old-style configured mappings produce correct result
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: Section app::service mapped by /service missing')]
    public function oldStyleMappingWithoutCorrespondingSection() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'mappings', '/service:service');

        create(new xp新criptlet愛unner('/htdocs'))->configure($p);
      }
    }

    /**
     * Verifies that configured mappings produce correct result
     *
     */
    #[@test]
    public function mappings() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.service', '/service');
        $p->writeString('app', 'map.global', '/');

        $p->writeSection('app::service');
        $p->writeSection('app::global');

        $r= new xp新criptlet愛unner('/htdocs');
        $r->configure($p);

        $this->assertEquals(
          array(
            '/service' => create(new WebApplication('service'))->withConfig('/htdocs/etc'), 
            '/'        => create(new WebApplication('global'))->withConfig('/htdocs/etc')
          ),
          $r->mappedApplications()
        );
      }
    }

    /**
     * Verifies that old-style configured mappings produce correct result
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: Section app::service mapped by /service missing')]
    public function mappingWithoutCorrespondingSection() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.service', '/service');
        create(new xp新criptlet愛unner('/htdocs'))->configure($p);
      }
    }

    /**
     * Creates a new runner
     *
     */
    protected function newRunner($profile= NULL) {
      $r= new xp新criptlet愛unner('/var/www', $profile);
      
      // The debug application
      $r->mapApplication('/debug', create(new WebApplication('debug'))
        ->withScriptlet(self::$debugScriptlet->getName())
        ->withConfig($r->expand('{WEBROOT}/etc/{PROFILE}'))
        ->withEnvironment(array('DOMAIN' => 'example.com', 'ADMINS' => 'admin@example.com,root@localhost'))
        ->withArguments(array('Debugging', 'today'))
      );

      // The error application
      $r->mapApplication('/error', create(new WebApplication('error'))
        ->withScriptlet(self::$errorScriptlet->getName())
        ->withConfig($r->expand('{WEBROOT}/etc'))
        ->withDebug('dev' === $profile 
          ? WebDebug::XML | WebDebug::ERRORS | WebDebug::STACKTRACE | WebDebug::TRACE
          : WebDebug::NONE
        )
      );

      // The incomplete app (missing a scriptlet)
      $r->mapApplication('/incomplete', create(new WebApplication('incomplete'))
        ->withScriptlet(NULL)
        ->withDebug(WebDebug::STACKTRACE)
      );

      // The XML application
      $r->mapApplication('/xml', create(new WebApplication('xml'))
        ->withScriptlet(self::$xmlScriptlet->getName())
        ->withDebug('dev' === $profile 
          ? WebDebug::XML 
          : WebDebug::NONE
        )
      );
      
      // The welcome application
      $r->mapApplication('/', create(new WebApplication('welcome'))
        ->withScriptlet(self::$welcomeScriptlet->getName())
        ->withConfig($r->expand('{WEBROOT}/etc'))
        ->withDebug('dev' === $profile 
          ? WebDebug::XML | WebDebug::ERRORS | WebDebug::STACKTRACE
          : WebDebug::NONE
        )
      );
      
      return $r;
    }

    /**
     * Test expand() method
     *
     */
    #[@test]
    public function expandServerProfile() {
      $this->assertEquals('etc/dev/', $this->newRunner('dev')->expand('etc/{PROFILE}/'));
    }

    /**
     * Test expand() method
     *
     */
    #[@test]
    public function expandWebRoot() {
      $this->assertEquals('/var/www/htdocs', $this->newRunner('dev')->expand('{WEBROOT}/htdocs'));
    }

    /**
     * Test expand() method
     *
     */
    #[@test]
    public function expandWebRootAndServerProfile() {
      $this->assertEquals('/var/www/etc/prod/', $this->newRunner('prod')->expand('{WEBROOT}/etc/{PROFILE}/'));
    }

    /**
     * Test expand() method
     *
     */
    #[@test]
    public function expandUnknownVariable() {
      $this->assertEquals('{ROOT}', $this->newRunner('prod')->expand('{ROOT}'));
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Could not find app responsible for request to /')]
    public function noApplication() {
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.service', '/service');
        $p->writeSection('app::service');

        $r= new xp新criptlet愛unner('/htdocs');
        $r->configure($p);
        $r->applicationAt('/');
      }
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function welcomeApplication() {
      $this->assertEquals(
        create(new WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet'),
        $this->newRunner()->applicationAt('/')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function welcomeApplicationAtEmptyUrl() {
      $this->assertEquals(
        create(new WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet'),
        $this->newRunner()->applicationAt('')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function welcomeApplicationAtDoubleSlash() {
      $this->assertEquals(
        create(new WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet'),
        $this->newRunner()->applicationAt('//')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function errorApplication() {
      $this->assertEquals(
        create(new WebApplication('error'))->withConfig('/var/www/etc')->withScriptlet('ErrorScriptlet'),
        $this->newRunner()->applicationAt('/error')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function welcomeApplicationAtUrlEvenWithErrorInside() {
      $this->assertEquals(
        create(new WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet'),
        $this->newRunner()->applicationAt('/url/with/error/inside')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function welcomeApplicationAtUrlBeginningWithErrors() {
      $this->assertEquals(
        create(new WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet'),
        $this->newRunner()->applicationAt('/errors')
      );
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function errorApplicationAtErrorPath() {
      $this->assertEquals(
        create(new WebApplication('error'))->withConfig('/var/www/etc')->withScriptlet('ErrorScriptlet'),
        $this->newRunner()->applicationAt('/error/happened')
      );
    }

    /**
     * Runs a scriptlet
     *
     * @param   string profile
     * @param   string url
     * @return  string content
     */
    protected function runWith($profile, $url) {
      ob_start();
      $this->newRunner($profile)->run($url);
      $content= ob_get_contents();
      ob_end_clean();
      return $content;
    }
    
    /**
     * Test normal page display
     *
     */
    #[@test]
    public function pageInProdMode() {
      $this->assertEquals(
        '<h1>Welcome, we are open</h1>', 
        $this->runWith('prod', '/')
      );
    }

    /**
     * Test normal page display
     *
     */
    #[@test]
    public function pageWithWarningsInProdMode() {
      $warning= 'Warning! Do not read if you have work to do!';
      with (trigger_error($warning)); {
        preg_match(
          '#'.preg_quote($warning).'#', 
          $this->runWith('prod', '/'),
          $matches
        );
        xp::gc(__FILE__);
      }
      $this->assertEquals(array(), $matches);
    }

    /**
     * Test normal page display with warnings
     *
     */
    #[@test]
    public function pageWithWarningsInDevMode() {
      $warning= 'Warning! Do not read if you have work to do!';
      with (trigger_error($warning)); {
        preg_match(
          '#'.preg_quote($warning).'#', 
          $this->runWith('dev', '/'),
          $matches
        );
        xp::gc(__FILE__);
      }
      $this->assertEquals($warning, $matches[0]);
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function errorPageInProdMode() {
      $content= $this->runWith('prod', '/error');
      preg_match('#<xmp>(.+)</xmp>#', $content, $matches);
      preg_match('#ERROR ([0-9]+)#', $content, $error);

      $this->assertEquals('500', $error[1], 'error message');
      $this->assertEquals(
        'Request processing failed [doGet]: No shoes, no shorts, no service', 
        $matches[1]
      );
    }

    /**
     * Asserts a given buffer contains the given bytes       
     *
     * @param   string bytes
     * @param   string buffer
     * @throws  unittest.AssertionFailedError
     */
    protected function assertContained($bytes, $buffer, $message= 'Not contained') {
      strstr($buffer, $bytes) || $this->fail($message, $buffer, $bytes);
    }

    /**
     * Asserts a given buffer does not contain the given bytes       
     *
     * @param   string bytes
     * @param   string buffer
     * @throws  unittest.AssertionFailedError
     */
    protected function assertNotContained($bytes, $buffer, $message= 'Contained') {
      strstr($buffer, $bytes) && $this->fail($message, $buffer, $bytes);
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function errorPageLoggingInProdMode() {
      with ($cat= Logger::getInstance()->getCategory('scriptlet')); {
        $appender= $cat->addAppender(new BufferedAppender());
        $this->runWith('prod', '/error');
        $buffer= $appender->getBuffer();
        $cat->removeAppender($appender);
        
        $this->assertNotContained(
          'Injected util.log.LogCategory',
          $buffer
        );
        $this->assertContained(
          'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
          $buffer
        );
      }
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function errorPageLoggingInDevMode() {
      with ($cat= Logger::getInstance()->getCategory('scriptlet')); {
        $appender= $cat->addAppender(new BufferedAppender());
        $this->runWith('dev', '/error');
        $buffer= $appender->getBuffer();
        $cat->removeAppender($appender);
        
        $this->assertContained(
          'Injected util.log.LogCategory',
          $buffer
        );
        $this->assertContained(
          'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
          $buffer
        );
      }
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function errorPageInDevMode() {
      $content= $this->runWith('dev', '/error');
      preg_match('#ERROR ([0-9]+)#', $content, $error);
      preg_match('#<xmp>(.+)#', $content, $compound);
      preg_match('#Caused by (.+)#', $content, $cause);

      $this->assertEquals('500', $error[1], 'error message');
      $this->assertEquals(
        'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
        $compound[1],
        'exception compound message'
      );
      $this->assertEquals(
        'Exception lang.IllegalAccessException (No shoes, no shorts, no service)',
        $cause[1],
        'exception cause'
      );
    }

    /**
     * Test debug page display
     *
     */
    #[@test]
    public function debugPage() {
      $content= $this->runWith('dev', '/debug');
      preg_match('#<h1>(.+)</h1>#', $content, $params);
      preg_match('#<h2>(.+)</h2>#', $content, $config);
      preg_match_all('#<li>(ENV\..+)</li>#U', $content, $env);

      $this->assertEquals('Debugging @ today', $params[1], 'params');
      $this->assertEquals('/var/www/etc/dev/debug.ini', $config[1], 'config');
      $this->assertEquals(
        array('ENV.DOMAIN = example.com', 'ENV.ADMINS = admin@example.com,root@localhost'),
        $env[1],
        'environment'
      );
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function incompleteApp() {
      $content= $this->runWith(NULL, '/incomplete');
      preg_match('#ERROR ([0-9]+)#', $content, $error);
      preg_match('#<xmp>(.+)#', $content, $compound);

      $this->assertEquals('412', $error[1], 'error message');
      $this->assertEquals(
        'Exception lang.ClassNotFoundException (Class "" could not be found) {', 
        $compound[1],
        'exception compound message'
      );
    }

    /**
     * Test XML app display
     *
     */
    #[@test]
    public function xmlScriptletAppInProdMode() {
      $content= $this->runWith('prod', '/xml');
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?><h1>Welcome, we are open</h1>',
        str_replace("\n", '', $content)
      );
    }

    /**
     * Test XML app display
     *
     */
    #[@test]
    public function xmlScriptletAppInDevMode() {
      $content= $this->runWith('dev', '/xml');
      preg_match('#<h1>(.+)</h1>#', $content, $output);
      preg_match('#<result>(.+)</result>#', $content, $source);
      
      $this->assertEquals('Welcome, we are open', $output[1], 'output');
      $this->assertEquals('Welcome, we are open', $source[1], 'source');
      $this->assertContained('<formresult', $content, 'formresult');
      $this->assertContained('<formvalues', $content, 'formvalues');
      $this->assertContained('<formerrors', $content, 'formerrors');
    }
  }
?>
