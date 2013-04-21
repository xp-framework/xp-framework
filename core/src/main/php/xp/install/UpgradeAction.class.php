<?php
  namespace xp\install;

  use \io\Folder;
  use \io\File;
  use \io\IOException;
  use \io\collections\FileCollection;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\iterate\ExtensionEqualsFilter;
  use \io\collections\iterate\NameMatchesFilter;
  use \io\streams\StringReader;
  use \util\cmd\Console;
  use \webservices\json\JsonFactory;
  use \webservices\rest\RestClient;
  use \webservices\rest\RestRequest;
  use \webservices\rest\RestException;

  /**
   * XPI Installer - add modules
   * ===========================
   *
   * Basic usage
   * -----------
   * # This will install the newest release of the specified module
   * $ xpi upgrade vendor/module
   */
  class UpgradeAction extends Action {
    protected static $json;

    static function __static() {
      self::$json= JsonFactory::create();
    }

    /**
     * Finds all installed releases
     *
     * @param  io.Folder $cwd Current working directory
     * @param  xp.install.Module $module
     * @return [:io.collections.FileCollection] The releases
     */
    protected function installedReleasesOf($cwd, $module) {
      $find= new NameMatchesFilter('/^'.$module->name.'@.+/');
      $base= new FileCollection(new Folder($cwd, $module->vendor));
      $releases= array();
      foreach (new FilteredIOCollectionIterator($base, $find) as $installed) {
        sscanf(basename($installed->getURI()), '%[^@]@%[^/\\]', $name, $version);
        $releases[$version]= $installed;
      }
      return $releases;
    }

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      sscanf($args[0], '%[^@]@%s', $name, $version);
      $module= Module::valueOf($name);

      $cwd= new Folder('.');
      if (NULL === $version) {

        // No version given: Search for newest version.
        $releases= $this->installedReleasesOf($cwd, $module);
        if (empty($releases)) {
          Console::$err->writeLine('*** Cannot find installed module ', $module);
          return 3;
        }

        uksort($releases, function($a, $b) { return version_compare($a, $b, '<'); });
        $version= key($releases);
        $installed= $releases[$version];

        Console::writeLine('Using newest installed version ', $version, ' -> ', $installed);
      } else {

        // Version given, select this for upgrading.
        $folder= new Folder($cwd, $module->vendor, $module->name.'@'.$version);
        if (!$folder->exists()) {
          Console::$err->writeLine('*** Cannot find installed module ', $module, ' in version ', $version);
          return 3;
        }

        $installed= new FileCollection($folder);
        Console::writeLine('Using specified version ', $version, ' -> ', $installed);
      }

      // Check for module online
      $request= create(new RestRequest('/vendors/{vendor}/modules/{module}/releases'))
        ->withSegment('vendor', $module->vendor)
        ->withSegment('module', $module->name)
      ;
      try {
        $releases= $this->api->execute($request)->data();
        usort($releases, function($a, $b) { 
          return version_compare($a['version']['number'], $b['version']['number'], '<'); 
        });
      } catch (RestException $e) {
        Console::$err->writeLine('*** Cannot find module ', $module, ': ', $e->getMessage());
        return 3;
      }

      // Verify we actually need an upgrade
      $newest= $releases[0]['version']['number'];
      if (version_compare($version, $newest, '>=')) {
        Console::writeLine('*** Already at newest version, no upgrade required: ', $releases[0]);
        return 1;
      }

      // Upgrade: Add new version, then remove old one
      Console::writeLine('>> Upgrading to ', $newest);
      $r= $this->spawn(new AddAction())->perform(array($module->vendor.'/'.$module->name, $newest));
      if (0 !== $r) return $r;
      $this->spawn(new RemoveAction())->perform(array($module->vendor.'/'.$module->name.'@'.$version));
      if (0 !== $r) return $r;
      return 0;
    }
  }
?>