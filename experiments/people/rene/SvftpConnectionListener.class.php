<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.ftp.server.FtpConnectionListener',
    'lang.Process'
  );
  
  /**
   * Special class for FTP over SVN
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SvftpConnectionListener extends FtpConnectionListener {
  
    /**
     * svn add via FTP
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onAdd($event, $params) {
      $this->cat->debug('svn add '.$this->pathCorrector($event, $params));
      $p= new Process('svn add '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $this->answer($event->stream, 200, Added files:, $output);
    }

    /**
     * svn rm via FTP
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onRemove($event, $params) {
      $this->cat->debug('svn rm --force '.$this->pathCorrector($event, $params));
      $this->cat->debug(var_dump($event));
      $p= new Process('svn rm --force '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'start', $output);
    }

    /**
     * svn add via FTP
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onCommit($event, $params) {
      $p= new Process('svn ci '.$this->pathCorrector($event, $params).' -m "rdoebele: test"', $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'SVN output:', $output);
    }

    /**
     * svn stat via FTP
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onStatus($event, $params) {
      $this->cat->debug('svn stat '.$this->pathCorrector($event, $params));
      $p= new Process('svn stat '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'start', $output);
    }    
    /**
     * svn revert
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onRevert($event, $params) {
      $p= new Process('svn revert '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'start', $output);
    }

    /**
     * Blame/praise
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onBlame($event, $params) {
      $p= new Process('svn blame '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'Who\'s to blame/praise?', $output);
    }

    /**
     * Blame/praise
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onUpdate($event, $params) {
      $this->storage->root;
      
      $p= new Process('svn up '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'Update:', $output);
    }

    /**
     * Diff
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onDiff($event, $params) {
      $p= new Process('svn diff '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'Update:', $output);
    }


    
    /**
     * List all Files under version controll
     *
     * @param   peer.server.ConnectionEvent event   
     */
    public function onLs($event, $params) {
      $p= new Process('svn list '.$this->pathCorrector($event, $params), $output, $errcode);
      while (!$p->out->eof()) {
        $output[]= $p->out->readLine();
      }
      $p->close();
      $this->answer($event->stream, 200, 'Files under version control:', $output);
    }
    
    /**
     * Get absolute path of the ftp root
     * within the filesystem
     *
     * @param   peer.server.ConnectionEvent event
     * @return  string
     */
    private function getAbsoluteURI($event) {
      $entry= $this->storage->lookup($event->stream->hashCode(), '');
      return rtrim($entry->root.$entry->getFilename(), DIRECTORY_SEPERATOR);
    }
    
    /**
     * Messy helper to correct the path of the params
     * so svn can find them.
     *
     * @param   peer.server.ConnectionEvent event
     * @return  string
     */
    
    private function pathCorrector($event, $params) {
      $path= $this->getAbsoluteURI($event);
      $files= explode(' ', $params);
      foreach ($files as $f) {
        $corrected[]= $path.$f;
      }
      return implode(' ', $corrected);
    }
  }
?>
