<?php
  namespace xp\install;

  use \util\cmd\Console;
  use \webservices\rest\RestRequest;
  use \webservices\rest\RestException;
  use \io\collections\FileCollection;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\iterate\NameMatchesFilter;
  use \io\File;
  use \io\Folder;
  use \io\IOException;
  use \io\streams\StringReader;
  use \webservices\json\JsonFactory;

  /**
   * XPI Installer - Module info
   * ===========================
   *
   * Basic usage
   * -----------
   * # This will show info about a module, remotely if not installed
   * $ xpi info vendor/module
   *
   * # This will show info about an installed module
   * $ xpi info -i vendor/module
   *
   * # This will show info about a remote module
   * $ xpi info -r vendor/module
   */
  class InfoAction extends Action {
    protected static $json;

    static function __static() {
      self::$json= JsonFactory::create();
    }

    protected function installedReleases($cwd, $module) {
      $releases= array();
      $vendor= $cwd->getCollection($module->vendor);
      $find= new NameMatchesFilter('/^'.$module->name.'@.+/');
      foreach (new FilteredIOCollectionIterator($vendor, $find) as $installed) {
        $uri= basename($installed->getURI());
        $pth= $cwd->findElement('.'.$module->vendor.'.'.$uri.'.pth');

        // Parse version
        sscanf($uri, '%[^@]@%[^/\\]', $name, $version);
        if (!($parts= sscanf($version, '%d.%d.%d%s'))) {
          $version= ':'.$version;
          $release= array('dev' => $version);
        } else {
          $rc= NULL;
          if (NULL === $parts[3]) {
            // Head release, e.g. 5.8.0
          } else if ('~' === $parts[3]{0}) {
            // Branch release, e.g. 5.8.0~unicode or 5.8.0~unicodeRC1
            $branch= NULL;
            sscanf($parts[3], '~%[a-z]RC%d', $branch, $rc);
          } else {
            // Release candidate, e.g. 5.8.0RC5
            sscanf($parts[3], 'RC%d', $rc);
          }
          $release= array('series' => $parts[0].'.'.$parts[1], 'rc' => $rc > 0 ? TRUE : FALSE);
        }

        if ($pth) {
          $release['classpath']= array();
          $r= new StringReader($pth->getInputStream());
          while (NULL !== ($line= $r->readLine())) {
            $resolved= realpath($cwd->getURI().ltrim($line, '!'));
            if (is_dir($resolved)) {
              $cl= \lang\FileSystemClassLoader::instanceFor($resolved, FALSE);
            } else if (is_file($resolved)) {
              $cl= \lang\archive\ArchiveClassLoader::instanceFor($resolved, FALSE);
            }
            $release['classpath'][]= $cl;
          }
        }
        $release['installed']= $installed->lastModified();
        $releases[$version]= $release;
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
      $cwd= new FileCollection('.');

      // Parse args
      if ('-r' === $args[0]) {
        $remote= TRUE;
        array_shift($args);
      } else if ('-i' === $args[0]) {
        $remote= FALSE;
        array_shift($args);
      } else {
        $remote= NULL;
      }

      sscanf(rtrim($args[0], '/'), '%[^@]@%s', $name, $version);
      $module= Module::valueOf($name);

      if (!$remote) {
        $releases= $this->installedReleases($cwd, $module);
        $remote= empty($releases);
      }

      // Search for module
      if ($remote) {
        Console::writeLine('@', $this->api->getBase()->getURL());
        $request= create(new RestRequest('/vendors/{vendor}/modules/{module}'))
          ->withSegment('vendor', $module->vendor)
          ->withSegment('module', $module->name)
        ;
        try {
          $result= $this->api->execute($request)->data();
        } catch (RestException $e) {
          Console::$err->writeLine('*** Cannot find remote module ', $module, ': ', $e->getMessage());
          return 3;
        }

        $releases= $result['releases'];
        uksort($releases, function($a, $b) {
          return version_compare($a, $b, '<');
        });
      } else {
        Console::writeLine('@', $cwd->getURI());

        uksort($releases, function($a, $b) {
          return version_compare($a, $b, '<');
        });
        try {
          $result= self::$json->decodeFrom(
            create(new File(new Folder($module->vendor, $module->name.'@'.key($releases)), 'module.json'))->getInputStream()
          );
        } catch (IOException $e) {
          Console::$err->writeLine('*** Cannot find installed module ', $module, ': ', $e->getMessage());
          return 3;
        }
      }

      Console::writeLine(new Module($result['vendor'], $result['module']), ': ', $result['info']);
      Console::writeLine($result['link']['url']);
      Console::writeLine('Releases: ', $releases);
      return 0;
    }
  }
?>