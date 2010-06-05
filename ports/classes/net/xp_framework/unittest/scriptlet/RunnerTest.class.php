<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.cmd.Command',
    'xp.scriptlet.Runner',
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
    
    static function __static() {
      self::$errorScriptlet= newinstance('scriptlet.HttpScriptlet', array(), '{
        protected function _request() {
          $req= new HttpScriptletRequest();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/members";
          $req->env["HTTP_HOST"]= "localhost";
          return $req;
        }
        
        protected function _setupRequest($request) {
          // Intentionally empty
        }
        
        public function doGet($request, $response) {
          throw new IllegalAccessException("No shoes, no shorts, no service");
        }
      }');
      self::$welcomeScriptlet= newinstance('scriptlet.HttpScriptlet', array(), '{
        protected function _request() {
          $req= new HttpScriptletRequest();
          $req->method= "GET";
          $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
          $req->env["REQUEST_URI"]= "/public";
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
    }    

    /**
     * Creates a runner
     *
     * @param   string profile
     * @return  xp.scriptlet.Runner
     */
    protected function _runner($profile= 'dev') {
      $prop= Properties::fromString('
[app]
mappings="/xml/:xml|/:global"

[app::xml]
class="scriptlet.xml.XMLScriptlet"
init-params="some.package|{WEBROOT}/xsl"
prop-base="etc/"

[app::xml@dev]
debug="XML|ERRORS|STACKTRACE"
prop-base="etc/dev/"
      ');

      return newinstance('xp.scriptlet.Runner', array($prop, '/webroot/doc_root/..', $profile), '{
        public function innerInvoke() {
          $args= func_get_args();
          $m= array_shift($args);
          return call_user_func_array(array($this, $m), $args);
        }

         public function getContent() {
           foreach ($this->readArray("app::xml", "debug", array()) as $lvl) {
             $this->flags |= $this->getClass()->getConstant($lvl);
           }
           ob_start();
           $this->run();
           $content= ob_get_contents();
           ob_end_clean();
           return $content;
         }

         public function withScriptlet($scriptlet) {
           $this->scriptlet= $scriptlet;
           return $this;
         }
      }');
    }

    /**
     * Test matching of URL against configuration works
     *
     */
    #[@test]
    public function findApplication() {
      $map= new Hashmap(array(
        '/some/url/' => 'app1',
        '/other/url' => 'app2',
        '/'          => 'app3'
      ));
      
      $this->assertEquals('app2', xp新criptlet愛unner::findApplication($map, '/other/url/with/appended.html'));
      $this->assertEquals('app1', xp新criptlet愛unner::findApplication($map, '/some/url/'));
      $this->assertEquals('app1', xp新criptlet愛unner::findApplication($map, '/some/url/below/'));
      $this->assertEquals('app3', xp新criptlet愛unner::findApplication($map, '/just/anything/falls/back'));
    }

    /**
     * Tests mapping with hashmap
     *
     */
    #[@test]
    public function mappingStyleHashmap() {
      $prop= Properties::fromString('
[app]
 mappings="/service/:service|/:global"
      ');

      $this->mappingTester(new xp新criptlet愛unner($prop, '/webroot/doc_root/..', 'dev'));
    }

    /**
     * Tests mapping with "." in keys
     *
     */
    #[@test]
    public function mappingStyleSection() {
      $prop= Properties::fromString('
[app]
 map.service="/service/"
 map.global="/"
      ');

      $this->mappingTester(new xp新criptlet愛unner($prop, '/webroot/doc_root/..', 'dev'));
    }

    /**
     * Helper method to test mapping styles for
     * same results
     *
     * @param   xp.scriptlet.Runner runner
     */
    protected function mappingTester($runner) {
      $this->assertEquals('service', $runner->activeSectionByMappings('/service/foo/bar'));
      $this->assertEquals('global', $runner->activeSectionByMappings('/whatever'));
      $this->assertEquals('global', $runner->activeSectionByMappings('/'));
    }

    /**
     * Verifies that empty mappings produce correct result
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function emptyMappings() {
      $prop= Properties::fromString('
[app]
no.mappings="TRUE"
      ');

      $this->mappingTester(new xp新criptlet愛unner($prop, '/webroot/doc_root/..', 'dev'));
    }

    /**
     * Creation
     *
     */
    #[@test]
    public function create() {
      $this->_runner();
    }

    /**
     * Test "{WEBROOT}" literal is being replaced
     *
     */
    #[@test]
    public function variableWebrootReplacement() {
      $this->assertEquals('/webroot/doc_root/../xsl/', $this->_runner()->replaceVariables('{WEBROOT}/xsl/'));
    }
    
    /**
     * Test "{PROFILE}" literal is being replaced
     *
     */
    #[@test]
    public function variableProfileReplacement() {
      $this->assertEquals('etc/dev/', $this->_runner()->replaceVariables('etc/{PROFILE}/'));
    }
    
    /**
     * Test profile-section values overwrite global value
     *
     */
    #[@test]
    public function profileSettingsOverwriteGlobalSettings() {
      $this->assertEquals('etc/dev/', $this->_runner()->innerInvoke('readString', 'app::xml', 'prop-base'));
    }
    
    /**
     * Test normal page display
     *
     */
    #[@test]
    public function page() {
      $this->assertEquals(
        '<h1>Welcome, we are open</h1>', 
        $this->_runner('prod')->withScriptlet(self::$welcomeScriptlet)->getContent()
      );
    }

    /**
     * Test normal page display
     *
     */
    #[@test]
    public function pageWithWarningsInProdMode() {
      $this->assertEquals(
        '<h1>Welcome, we are open</h1>', 
        $this->_runner('prod')->withScriptlet(self::$welcomeScriptlet)->getContent()
      );
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
          $this->_runner('dev')->withScriptlet(self::$welcomeScriptlet)->getContent(), 
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
      preg_match(
        '#<xmp>(.+)</xmp>#', 
        $this->_runner('prod')->withScriptlet(self::$errorScriptlet)->getContent(),
        $matches
      );
      $this->assertEquals(
        'Request processing failed [doGet]: No shoes, no shorts, no service', 
        $matches[1]
      );
    }

    /**
     * Test error page display
     *
     */
    #[@test]
    public function errorPageInDevMode() {
      $content= $this->_runner('dev')->withScriptlet(self::$errorScriptlet)->getContent();
      preg_match('#<xmp>(.+)#', $content, $compound);
      preg_match('#Caused by (.+)#', $content, $cause);

      $this->assertEquals(
        'Exception scriptlet.HttpScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
        $compound[1],
        'exception compound message'
      );
      $this->assertEquals(
        'Exception lang.IllegalAccessException (No shoes, no shorts, no service)',
        $cause[1],
        'exception cause'
      );
    }
  }
?>
