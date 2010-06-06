<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.scriptlet.Runner',
    'util.log.Traceable',
    'util.log.BufferedAppender',
    'scriptlet.HttpScriptlet'
  );

  $package= 'net.xp_framework.unittest.scriptlet';
  
  /**
   * TestCase
   *
   * @purpose  Unittest
   */
  class net暖p_framework暉nittest新criptlet愛unnerTest extends TestCase {
    protected static $welcomeScriptlet= NULL;
    protected static $errorScriptlet= NULL;
    protected static $debugScriptlet= NULL;
    
    static function __static() {
      self::$errorScriptlet= ClassLoader::defineClass('ErrorScriptlet', 'scriptlet.HttpScriptlet', array('util.log.Traceable'), '{
        protected function _request() {
          $req= new HttpScriptletRequest();
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
          $req= new HttpScriptletRequest();
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
      self::$debugScriptlet= ClassLoader::defineClass('DebugScriptlet', 'scriptlet.HttpScriptlet', array(), '{
        protected $title, $date;

        public function __construct($title, $date) {
          $this->title= $title;
          $this->date= $date;
        }
        
        protected function _request() {
          $req= new HttpScriptletRequest();
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
      with ($p= Properties::fromString('')); {
        $p->writeSection('app');
        $p->writeString('app', 'map.debug', '/debug');
        $p->writeString('app', 'map.error', '/error');
        $p->writeString('app', 'map.welcome', '/');

        // The debug app
        $p->writeSection('app::debug');
        $p->writeString('app::debug', 'class', self::$debugScriptlet->getName());
        $p->writeString('app::debug', 'prop-base', '{WEBROOT}/etc/{PROFILE}');
        $p->writeString('app::debug', 'init-envs', 'DOMAIN:example.com|ADMINS:admin@example.com,root@localhost');
        $p->writeString('app::debug', 'init-params', 'Debugging|today');

        // The error app
        $p->writeSection('app::error');
        $p->writeString('app::error', 'class', self::$errorScriptlet->getName());
        $p->writeSection('app::error@dev');
        $p->writeString('app::error@dev', 'debug', 'XML|ERRORS|STACKTRACE|TRACE');

        // The welcome app
        $p->writeSection('app::welcome');
        $p->writeString('app::welcome', 'class', self::$welcomeScriptlet->getName());
        $p->writeSection('app::welcome@dev');
        $p->writeArray('app::welcome@dev', 'debug', 'XML|ERRORS|STACKTRACE');

        $r= new xp新criptlet愛unner('/var/www', $profile);
        $r->configure($p);
        return $r;
      }
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
      preg_match('#<xmp>(.+)</xmp>#', $this->runWith('prod', '/error'), $matches);
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
      preg_match('#<xmp>(.+)#', $content, $compound);
      preg_match('#Caused by (.+)#', $content, $cause);

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
  }
?>
