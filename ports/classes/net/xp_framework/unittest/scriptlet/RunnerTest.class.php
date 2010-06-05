<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.cmd.Command',
    'xp.scriptlet.Runner'
  );

  $package= 'net.xp_framework.unittest.scriptlet';
  
  /**
   * TestCase
   *
   * @purpose  Unittest
   */
  class net暖p_framework暉nittest新criptlet愛unnerTest extends TestCase {

    /**
     * Creates a runner
     *
     * @return  xp.scriptlet.Runner
     */
    protected function _runner() {
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

      return newinstance('xp.scriptlet.Runner', array($prop, '/webroot/doc_root/..', 'dev'), '{
        public function innerInvoke() {
          $args= func_get_args();
          $m= array_shift($args);
          return call_user_func_array(array($this, $m), $args);
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
     * @param   xp新criptlet愛unner runner
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
  }
?>
