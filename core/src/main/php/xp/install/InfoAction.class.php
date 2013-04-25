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

    /**
     * Gets all installed releases for a given module
     *
     * @param  io.collections.IOCollection $cwd Working directory
     * @param  xp.install.Module $module
     * @return var[]
     */
    protected function installedReleases($cwd, $module) {
      $releases= array();
      $vendor= $cwd->getCollection($module->vendor);
      $find= new NameMatchesFilter('/^'.$module->name.'@.+/');
      foreach (new FilteredIOCollectionIterator($vendor, $find) as $installed) {
        sscanf(basename($installed->getURI()), '%[^@]@%[^/\\]', $name, $version);
        if (!($parts= sscanf($version, '%d.%d.%d%s'))) {
          $release= array('dev' => $version);
          $version= ':'.$version;
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
        $installed= new File($args[0].'.json');
      } else {
        $installed= new File($args[0].'.json');
        $remote= !$installed->exists();
      }

      sscanf(rtrim($args[0], '/'), '%[^@]@%s', $name, $version);
      $module= Module::valueOf($name);

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
      } else {
        Console::writeLine('@', $cwd->getURI());

        try {
          $result= self::$json->decodeFrom($installed->getInputStream());
        } catch (IOException $e) {
          Console::$err->writeLine('*** Cannot find installed module ', $module, ': ', $e->getMessage());
          return 3;
        }

        $releases= $this->installedReleases($cwd, $module);
      }

      Console::writeLine(new Module($result['vendor'], $result['module']), ': ', $result['info']);
      Console::writeLine($result['link']['url']);
      uksort($releases, function($a, $b) {
        return version_compare($a, $b, '<');
      });
      Console::writeLine('Releases: ', sizeof($releases), ', list {');
      foreach ($releases as $version => $release) {
        $s= '';
        foreach ($release as $key => $value) {
          $s.= ', '.$key.'= '.\xp::stringOf($value);
        }
        Console::writeLine('  ', $version, ' (', substr($s, 2), ')');
      }
      Console::writeLine('}');

      // List active releases for local queries
      if (!$remote) {
        foreach (new FilteredIOCollectionIterator($cwd, new NameMatchesFilter('#^\.'.$module->vendor.'\.'.$module->name.'.*\.pth#')) as $found) {
          $r= new StringReader($found->getInputStream());
          Console::writeLine('Selected: ', basename($found->getURI()), ', class path {');
          while (NULL !== ($line= $r->readLine())) {
            $resolved= realpath($cwd->getURI().ltrim($line, '!'));
            if (is_dir($resolved)) {
              $cl= \lang\FileSystemClassLoader::instanceFor($resolved, FALSE);
            } else if (is_file($resolved)) {
              $cl= \lang\archive\ArchiveClassLoader::instanceFor($resolved, FALSE);
            }
            Console::writeLine('  ', $cl);
          }
          Console::writeLine('}');
        }
        return 0;
      }
    }
  }
?>