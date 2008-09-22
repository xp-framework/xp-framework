<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.cmd.Command', 
    'remote.Remote', 
    'net.xp_framework.website.planet.cmd.IndexerDocument'
  );

  /**
   * Indexer
   *
   * @purpose  Base class
   */
  abstract class Indexer extends Command {
    protected
      $writer   = NULL,
      $index    = '',
      $verbose  = FALSE;
    
    const
      UPDATE    = '.',
      DELETE    = 'd';
  
    /**
     * Returns the index iterator
     *
     * @return  de.schlund.intranet.search.index.ImportIterator
     */
    protected abstract function importIterator();
    
    /**
     * Retrieve indexerHint for this run
     *
     * @return  string
     */
    protected function indexerHint() {
      if (!$this->hint) $this->hint= (string)time();
      
      return $this->hint;
    }
 
    /**
     * Set index name
     *
     * @param   string index
     */
    #[@arg]
    public function setIndexName($index) {
      $this->index= $index;
    }
 
    /**
     * Set remote host
     *
     * @param   string hostspec
     */
    #[@arg]
    public function setRemoteHost($hostspec) {
      $this->writer= Remote::forName('xp://'.$hostspec)->lookup('lucene/Writer');
      try {
        $this->writer->openIndex($this->index, FALSE);
      } catch (RemoteException $e) {    // XXX: Check for cause
        $this->err->writeLine('*** Could not open index "', $this->index, '", reopening with create');
        $this->writer->openIndex($this->index, TRUE);
      }
    }

    /**
     * Set whether to be verbose (default: no)
     *
     */
    #[@arg]
    public function setVerbose() {
      $this->verbose= TRUE;
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $iterator= $this->importIterator();
      $this->verbose && $this->out->writeLine('===> Importing for index "', $this->index, '" using ', $iterator);

      $imported= 0; $type= NULL;
      while ($iterator->hasNext()) {
        $document= $iterator->next();
        $type= $document->document->getType();

        $this->verbose && $this->out->write($document->getOperation());
        $this->verbose && $this->out->write('->(', $document, ')');

        try {
          switch ($document->getOperation()) {
            case IndexerDocument::DELETE:
              $this->writer->deleteDocument($this->index, $document->document);
              break;
            
            case IndexerDocument::UPDATE:
              $this->writer->updateDocument($this->index, $document->document, $this->indexerHint());
              break;
            
            default:
              throw new IllegalArgumentException('Unknown operation '.$this->operation);
          }
        } catch (RemoteException $e) {
          $this->err->writeLine('*** Error for ', $document);
          throw $e;
        }
        $imported++;
      }
      
      if (NULL !== $type) {
        $this->writer->cleanDocuments($this->index, $type, $this->indexerHint());
      }
      
      $this->verbose && $this->out->writeLine();
      $this->verbose && $this->out->writeLine('===> Imported ', $imported, ' entries');
      $this->writer && $this->writer->closeIndex($this->index);
    }
  }
?>
