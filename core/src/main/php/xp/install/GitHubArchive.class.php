<?php
  namespace xp\install;

  use \peer\http\HttpConnection;
  use \peer\http\HttpConstants;
  use \io\archive\zip\ZipFile;
  use \io\streams\StreamTransfer;
  use \io\Folder;
  use \io\File;
  use \util\cmd\Console;

  /**
   * GitHub Archive - uses https://github.com/vendor/repo/zipball/branch 
   * for downloading. This URL redirects (possibly multiple times).
   */
  class GitHubArchive extends \lang\Object implements Origin {
    private $url;

    /**
     * Creates a new instance
     *
     * @param string $vendor
     * @param string $module
     * @param string $branch
     */
    public function __construct($vendor, $module, $branch) {
      $this->url= sprintf('https://github.com/%s/%s/zipball/%s', $vendor, $module, $branch);
    }

    /**
     * Creates a HTTP connection. Uses a timeout of 10 seconds at github
     * is a bit slow in responding from while to while
     *
     * @param   string url
     * @return  peer.http.HttpConnection 
     */
    protected function connectionTo($url) {
      $conn= new HttpConnection($url);
      $conn->setConnectTimeout(10.0);
      return $conn;
    }

    /**
     * Fetches a ZIP file
     *
     * @param   string url
     * @return  io.archive.zip.ZipArchiveReader
     */
    protected function zipballOf($url) {
      $headers= array();
      do {
        Console::write('>> ', $url, ': ');
        $response= $this->connectionTo($url, $headers)->get();
        switch ($response->statusCode()) {
          case HttpConstants::STATUS_OK:
            Console::writeLine('Ok');
            return ZipFile::open($response->getInputStream());

          case HttpConstants::STATUS_FOUND: case HttpConstants::STATUS_SEE_OTHER:
            Console::writeLine('Redirect');
            $headers['Referer']= $url;
            $url= this($response->header('Location'), 0);
            continue;

          default:
            Console::writeLine('Error');
            throw new IllegalStateException('Unexpected response for '.$url.': '.$response.toString());
        }
      } while (1);

      // Unreachable
    }

    /**
     * Fetches this origin into a given target folder
     *
     * @param  io.Folder $target
     */
    public function fetchInto(Folder $target) {
      $zip= $this->zipBallOf($this->url);
      $i= 0;
      with ($iter= $zip->iterator()); {
        $base= rtrim($iter->next()->getName().'/', '/');
        Console::write('Extracting (', $base, ') [');
        while ($iter->hasNext()) {
          $entry= $iter->next();
          $relative= str_replace($base, '', $entry->getName());
          if ($entry->isDirectory()) {
            $folder= new Folder($target, $relative);
            $folder->exists() || $folder->create(0755);
          } else {
            $file= new File($target, $relative);
            $tran= new StreamTransfer($entry->getInputStream(), $file->getOutputStream());
            $tran->transferAll();
            $tran->close();
          }
          $i++ % 10 || Console::write('.');
        }
      }
      $zip->close();
      Console::writeLine(']');
    }
  }
?>