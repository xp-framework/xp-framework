<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.scriptlet';
  
  uses(
    'unittest.TestCase',
    'xp.scriptlet.WebConfiguration'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.scriptlet.WebConfiguration
   */
  class net·xp_framework·unittest·scriptlet·WebConfigurationTest extends TestCase {

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

        $this->assertEquals(
          array('/service' => create(new WebApplication('service'))
            ->withConfig('{WEBROOT}/etc/{PROFILE}')
            ->withScriptlet('ServiceScriptlet')
            ->withEnvironment(array('ROLE' => 'admin', 'CLUSTER' => 'a'))
            ->withDebug(WebDebug::STACKTRACE | WebDebug::ERRORS)
            ->withArguments(array('a', 'b'))
          ),
          create(new xp·scriptlet·WebConfiguration($p))->mappedApplications('dev')
        );
      }
    }
  }
?>
