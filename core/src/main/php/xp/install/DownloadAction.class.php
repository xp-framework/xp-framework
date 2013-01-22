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
    'io.streams.BufferedInputStream',
    'util.Properties'
  );

  /**
   * Downloads a file
   *
   */
  class DownloadAction extends Object {
    const BASE_URL = 'http://releases.xp-framework.net/download/';
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
      with ($target= new Folder($args[1])); {
        $target->exists() || $target->create();
        $this->extract(self::BASE_URL, $args[0], $target);
      }
    }
  }
?>
