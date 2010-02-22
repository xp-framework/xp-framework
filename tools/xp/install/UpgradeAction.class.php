<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.install.Installation', 
    'lang.Runtime', 
    'io.Folder',
    'io.File',
    'peer.http.HttpConnection',
    'peer.http.RequestData',
    'io.streams.InputStream',
    'io.streams.BufferedInputStream'
  );

  /**
   * Upgrades XP Framework and libraries
   *
   * Usage:
   * <pre>
   *   # Upgrade XP Framework release currently in use
   *   $ xpi upgrade
   *
   *   # Upgrade XP Framework release in a certain directory
   *   $ xpi upgrade /path/to/xp/5.7.6/
   * </pre>
   *
   * In both case, the previous version will be retained; the newer
   * version will be installed in parallel.
   */
  class UpgradeAction extends Object {
    const UPGRADE_URL = 'http://releases.xp-framework.net/upgrade/';
    const PROGRESS_INDICATOR_WIDTH= 10;
  
    /**
     * Read a line
     *
     * @param   io.streams.InputStream in
     * @return  string
     * @throws  lang.FormatException if no line can be read
     */
    protected function readLine(InputStream $in) {
      $line= '';
      while ("\n" !== ($chr= $in->read(1))) {
        $line.= $chr;
        if (!$in->available()) break;
      }
      return $line;
    }
    
    /**
     * Extract a ".ar" file into a given target directory
     *
     * @param   string base
     * @param   string ar
     * @param   io.Folder target
     * @throws  lang.IllegalStateException in case the target is not found
     * @throws  lang.FormatException in case the .ar-file is not parseable
     */
    protected function extract($base, $ar, Folder $target) {

      // Open a HTTP connection
      $url= new URL($base.$ar.'.ar');
      $r= create(new HttpConnection($url))->get();
      if (HttpConstants::STATUS_OK != $r->getStatusCode()) {
        throw new IllegalStateException(sprintf(
          'Unexpected response %d:%s for %s',
          $r->getStatusCode(),
          $r->getMessage(),
          $url->getURL()
        ));
      }
      
      $in= new BufferedInputStream($r->getInputStream());
      do {

        // Seach for first section header, --[LENGTH]:[FILENAME]-- and parse it
        do {
          $line= $this->readLine($in);
          if (!$in->available()) {
            throw new FormatException('Cannot locate section header');
          }
        } while (2 !== sscanf($line, '--%d:%[^:]--', $length, $filename));
      
        // Calculate target file
        $file= new File($target, $filename);
        $folder= new Folder($file->getPath());
        $folder->exists() || $folder->create();
        Console::writef(
          '     >> [%-10s] %s (%.2f kB) [%s]%s', 
          $ar,
          $filename, 
          $length / 1024, 
          str_repeat('.', self::PROGRESS_INDICATOR_WIDTH),
          str_repeat("\x08", self::PROGRESS_INDICATOR_WIDTH+ 1)
        );
        
        // Transfer length bytes into file
        $c= 0;
        $out= $file->getOutputStream();
        $size= 0;
        while ($size < $length) {
          $chunk= $in->read(min(0x1000, $length - $size));
          $size+= strlen($chunk);
          $out->write($chunk);
          
          // Update progress
          $d= ceil(($size / $length) * self::PROGRESS_INDICATOR_WIDTH);
          if ($d == $c) continue;
          Console::write(str_repeat('#', $d- $c));
          $c= $d;
        }
        $out->close();
        Console::writeLine();
      
      } while ($in->available() > 0);

      $in->close();
    }
    
    /**
     * Perform this action
     *
     * @param   string[] args
     */
    public function perform(array $args) {
      $installation= new Installation();
      $installation->setBase(new Folder(isset($args[0]) ? $args[0] : dirname(Runtime::getInstance()->bootstrapScript()).'/..'));
      with ($version= $installation->getVersion()); {
        Console::writeLine('===> Local version ', $version, ' @ ', $installation->getBase());
        if (strstr($version, '-dev')) {
          Console::writeLine('*** Cannot update development checkouts');
          return 2;
        }
      
        // Query releases website for newer versions
        $c= new HttpConnection(self::UPGRADE_URL);
        $r= $c->get(new RequestData($version));
        
        switch ($r->getStatusCode()) {
          case HttpConstants::STATUS_OK: case HttpConstants::STATUS_NOT_EXTENDED: {
            Console::writeLine('*** ', $this->readLine($r->getInputStream()));
            return 2;
          }
        
          case HttpConstants::STATUS_SEE_OTHER: {
            $upgrade= $r->getHeader('X-Upgrade-To');
            $base= $r->getHeader('Location');
            Console::writeLine('---> Upgrading to ', $upgrade, ' (', $base, ')');
            
            // Download base, tools, libraries and meta information
            $target= new Folder($installation->getBase(), $upgrade);
            $target->exists() || $target->create();
            $this->extract($base, 'base', $target);
            $this->extract($base, 'tools', $target);
            $this->extract($base, 'lib', $target);
            $this->extract($base, 'meta-inf', $target);
            
            // Verify it works vby running the XP Framework core unittests
            Console::writeLine('---> Running tests with [USE_XP=', $upgrade, ']:');
            $rt= Runtime::getInstance();
            set_include_path(implode(PATH_SEPARATOR, array(
              rtrim($target->getURI(), DIRECTORY_SEPARATOR),
              '',
              $target->getURI().'lib'.DIRECTORY_SEPARATOR.'xp-net.xp_framework-'.$upgrade.'.xar'
            )));
            $tests= array('net.xp_framework.unittest.core.**');
            with ($p= $rt->newInstance($rt->startupOptions(), 'class', 'xp.unittest.Runner', $tests)); {
              $p->in->close();
              while (!$p->out->eof()) { Console::writeLine($p->out->readLine()); }
              while (!$p->err->eof()) { Console::writeLine($p->err->readLine()); }
              $exit= $p->close();
            }
            
            // Nonzero exit means failure
            if (0 !== $exit) {
              Console::writeLine('*** Test run failed, please consult above error messsages');
              Console::writeLine($p->getCommandLine());
              return 2;
            }
            
            Console::writeLine('===> Done');
            return 0;
          }

          default: {
            throw new IllegalStateException('Unexpected response '.xp::stringOf($r));
          }
        }
      }
    }
  }
?>
