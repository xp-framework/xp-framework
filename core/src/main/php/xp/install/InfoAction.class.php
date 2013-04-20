<?php
  namespace xp\install;

  use \util\cmd\Console;
  use \webservices\rest\RestRequest;
  use \webservices\rest\RestException;

  /**
   * XPI Installer - Module info
   * ===========================
   *
   * Basic usage
   * -----------
   * # This will show info about a given module
   * $ xpi info vendor/module
   */
  class InfoAction extends Action {

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      sscanf($args[0], '%[^@]@%*s', $name);   // Be tab-completion friendly: remove after "@""
      $module= Module::valueOf($name);

      // Search for module online
      $request= create(new RestRequest('/vendors/{vendor}/modules/{module}'))
        ->withSegment('vendor', $module->vendor)
        ->withSegment('module', $module->name)
      ;
      try {
        $result= $this->api->execute($request)->data();
        uksort($result['releases'], function($a, $b) {
          return version_compare($a, $b, '<');
        });
      } catch (RestException $e) {
        Console::$err->writeLine('*** Cannot find module ', $module, ': ', $e->getMessage());
        return 3;
      }

      Console::writeLine(new Module($result['vendor'], $result['module']), ': ', $result['info']);
      Console::writeLine($result['link']['url']);
      Console::writeLine('Releases: ', $result['releases']);
      return 0;
    }
  }
?>