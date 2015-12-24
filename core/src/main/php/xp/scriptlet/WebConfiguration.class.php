<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.scriptlet';

  uses(
    'util.Properties',
    'util.Hashmap',
    'xp.scriptlet.WebDebug',
    'xp.scriptlet.WebApplication'
  );
  
  /**
   * Web application configuration
   *
   * @see   xp://xp.scriptlet.WebApplication
   * @test  xp://net.xp_framework.unittest.scriptlet.WebConfigurationTest
   */
  class xp·scriptlet·WebConfiguration extends Object {
    protected $prop= NULL;
    
    /**
     * Creates a new web configuration instance
     *
     * @param   util.Properties prop
     */
    public function __construct(Properties $prop) {
      $this->prop= $prop;
    }

    /**
     * Read string. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string profile
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readString($profile, $section, $key, $default= NULL) {
      if (NULL === ($s= $this->prop->readString($section.'@'.$profile, $key, NULL))) {
        return $this->prop->readString($section, $key, $default);
      }
      return $s;
    }
    
    /**
     * Read array. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string profile
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string[]
     */
    protected function readArray($profile, $section, $key, $default= NULL) {
      if (NULL === ($a= $this->prop->readArray($section.'@'.$profile, $key, NULL))) {
        return $this->prop->readArray($section, $key, $default);
      }
      return $a;
    }
    
    /**
     * Read hashmap. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string profile
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  util.Hashmap
     */
    protected function readHash($profile, $section, $key, $default= NULL) {
      if (NULL === ($h= $this->prop->readHash($section.'@'.$profile, $key, NULL))) {
        return $this->prop->readHash($section, $key, $default);
      }
      return $h;
    }
    
    /**
     * Creates a web application object from a given configuration section
     *
     * @param   string profile
     * @param   string application app name
     * @param   string url
     * @return  xp.scriptlet.WebApplication
     * @throws  lang.IllegalStateException if the web is misconfigured
     */
    protected function configuredApp($profile, $application, $url) {
      $section= 'app::'.$application;
      if (!$this->prop->hasSection($section)) {
        throw new IllegalStateException('Web misconfigured: Section '.$section.' mapped by '.$url.' missing');
      }

      $app= new WebApplication($application);
      $app->setScriptlet($this->readString($profile, $section, 'class', ''));
      
      // Configuration base
      $app->setConfig($this->readString($profile, $section, 'prop-base', '{WEBROOT}/etc'));

      // Determine debug level
      $flags= WebDebug::NONE;
      foreach ($this->readArray($profile, $section, 'debug', array()) as $lvl) {
        $flags |= WebDebug::flagNamed($lvl);
      }
      $app->setDebug($flags);
      
      // Initialization arguments
      $app->setArguments($this->readArray($profile, $section, 'init-params', array()));
 
      // Environment
      $app->setEnvironment($this->readHash($profile, $section, 'init-envs', new Hashmap())->toArray());
     
      return $app;
    }
    
    /**
     * Gets all mapped applications
     *
     * @param   string profile
     * @return  [:xp.scriptlet.WebApplication]
     * @throws  lang.IllegalStateException if the web is misconfigured
     */
    public function mappedApplications($profile= NULL) {
      $mappings= $this->prop->readHash('app', 'mappings', NULL);
      $apps= array();

      // Verify configuration
      if (NULL === $mappings) {
        foreach ($this->prop->readSection('app') as $key => $url) {
          if (0 !== strncmp('map.', $key, 4)) continue;
          $apps[$url]= $this->configuredApp($profile, substr($key, 4), $url);
        }
      } else {
        foreach ($mappings->keys() as $url) {
          $apps[$url]= $this->configuredApp($profile, $mappings->get($url), $url);
        }
      }

      if (0 === sizeof($apps)) {
        throw new IllegalStateException('Web misconfigured: "app" section missing or broken');
      }

      return $apps;
    }
  }
?>
