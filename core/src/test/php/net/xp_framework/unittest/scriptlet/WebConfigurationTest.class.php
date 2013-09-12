<?php namespace net\xp_framework\unittest\scriptlet;



use unittest\TestCase;
use xp\scriptlet\WebConfiguration;


/**
 * TestCase
 *
 * @see   xp://xp.scriptlet.WebConfiguration
 */
class WebConfigurationTest extends TestCase {

  /**
   * Verifies configure() method with all possible settings
   *
   */
  #[@test]
  public function configure() {
    with ($p= \util\Properties::fromString('')); {
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
        array('/service' => create(new \xp\scriptlet\WebApplication('service'))
          ->withConfig('{WEBROOT}/etc/{PROFILE}')
          ->withScriptlet('ServiceScriptlet')
          ->withEnvironment(array('ROLE' => 'admin', 'CLUSTER' => 'a'))
          ->withDebug(\xp\scriptlet\WebDebug::STACKTRACE | \xp\scriptlet\WebDebug::ERRORS)
          ->withArguments(array('a', 'b'))
        ),
        create(new WebConfiguration($p))->mappedApplications('dev')
      );
    }
  }

  /**
   * Verifies unknown debug flag in configuration raises an exception
   *
   */
  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'No flag named WebDebug::UNKNOWN')]
  public function configureWithUnknownDebugFlag() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'map.service', '/service');
      $p->writeSection('app::service');
      $p->writeString('app::service', 'debug', 'UNKNOWN');

      create(new WebConfiguration($p))->mappedApplications();
    }
  }

  /**
   * Verifies that empty configured mappings produce correct result
   *
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: "app" section missing or broken')]
  public function emptyMappings() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');

      create(new WebConfiguration($p))->mappedApplications();
    }
  }

  /**
   * Verifies that empty configured mappings produce correct result
   *
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: "app" section missing or broken')]
  public function appSectionWithoutValidMappings() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'not.a.mapping', 1);

      create(new WebConfiguration($p))->mappedApplications();
    }
  }

  /**
   * Verifies that old-style configured mappings produce correct result
   *
   */
  #[@test]
  public function oldStyleMappings() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'mappings', '/service:service|/:global');

      $p->writeSection('app::service');
      $p->writeSection('app::global');

      $this->assertEquals(
        array(
          '/service' => create(new \xp\scriptlet\WebApplication('service'))->withConfig('{WEBROOT}/etc'), 
          '/'        => create(new \xp\scriptlet\WebApplication('global'))->withConfig('{WEBROOT}/etc')
        ),
        create(new WebConfiguration($p))->mappedApplications()
      );
    }
  }

  /**
   * Verifies that old-style configured mappings produce correct result
   *
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: Section app::service mapped by /service missing')]
  public function oldStyleMappingWithoutCorrespondingSection() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'mappings', '/service:service');

      create(new WebConfiguration($p))->mappedApplications();
    }
  }

  /**
   * Verifies that configured mappings produce correct result
   *
   */
  #[@test]
  public function mappings() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'map.service', '/service');
      $p->writeString('app', 'map.global', '/');

      $p->writeSection('app::service');
      $p->writeSection('app::global');

      $this->assertEquals(
        array(
          '/service' => create(new \xp\scriptlet\WebApplication('service'))->withConfig('{WEBROOT}/etc'), 
          '/'        => create(new \xp\scriptlet\WebApplication('global'))->withConfig('{WEBROOT}/etc')
        ),
        create(new WebConfiguration($p))->mappedApplications()
      );
    }
  }

  /**
   * Verifies that old-style configured mappings produce correct result
   *
   */
  #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'Web misconfigured: Section app::service mapped by /service missing')]
  public function mappingWithoutCorrespondingSection() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'map.service', '/service');

      create(new WebConfiguration($p))->mappedApplications();
    }
  }
}
