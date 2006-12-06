<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'de.thekid.dialog.io.FilteredFolderIterator',
    'de.thekid.dialog.Album',
    'de.thekid.dialog.Update',
    'de.thekid.dialog.SingleShot',
    'de.thekid.dialog.EntryCollection',
    'util.log.Traceable'
  );

  /**
   * Creates indexes
   *
   * @purpose  Helper class
   */
  class IndexCreator extends Object implements Traceable {
    public
      $cat            = NULL,
      $folder         = NULL,
      $entriesPerPage = 5;

    /**
     * Set entriesPerPage
     *
     * @access  public
     * @param   int entriesPerPage
     */
    public function setEntriesPerPage($entriesPerPage) {
      $this->entriesPerPage= $entriesPerPage;
    }

    /**
     * Get entriesPerPage
     *
     * @access  public
     * @return  int
     */
    public function getEntriesPerPage() {
      return $this->entriesPerPage;
    }
    
    /**
     * Returns a IndexCreator for a given folder
     *
     * @model   static
     * @access  public
     * @param   &io.Folder folder
     * @return  &de.thekid.dialog.io.IndexCreator
     */
    public static function &forFolder(&$folder) {
      $i= new IndexCreator();
      $i->folder= &$folder;
      return $i;
    }
    
    /**
     * Regenerate
     *
     * @access  public
     * @return  bool success
     * @throws  io.IOException
     */
    public function regenerate() {
      $entries= array();
      for ($i= new FilteredFolderIterator($this->folder, '/\.dat$/'); $i->hasNext(); ) {
        $entry= $i->next();
        $file= new File($entry);
        try {
          $data= &unserialize(FileUtil::getContents($file));
        } catch (IOException $e) {
          $e->printStackTrace();
          exit(-1);
        }

        $date= &$data->getDate();
        $this->cat && $this->cat->debugf(
          '---> %s "%s" @ %s', 
          xp::typeOf($data),
          $entry,
          xp::stringOf($date)
        );
        $entries[$date->toString('YmdHis-').basename($entry)]= basename($entry, '.dat');
        delete($data);
      }
      $this->folder->close();
      krsort($entries);

      // ...by pages. The index "page_0" can be used for the home page
      for ($i= 0, $s= sizeof($entries); $i < $s; $i+= $this->entriesPerPage) {
        $this->cat && $this->cat->debugf(
          '---> Generating index for album #%d - #%d', 
          $i, 
          $i+ $this->entriesPerPage
        );

        try {
          FileUtil::setContents(
            new File($this->folder->getURI().'page_'.($i / $this->entriesPerPage).'.idx'), 
            serialize(array(
              'total'   => $s, 
              'perpage' => $this->entriesPerPage,
              'entries' => array_slice($entries, $i, $this->entriesPerPage)
            ))
          );
        } catch (IOException $e) {
          throw($e);
        }
      }
      
      // ...by album name, for album -> page lookup
      foreach (array_keys($entries) as $i => $key) {
        $page= intval(floor($i / $this->entriesPerPage));
        $this->cat && $this->cat->debugf(
          '---> Element %s is on page %d',
          $entries[$key],
          $page
        );
        
        try {
          FileUtil::setContents(
            new File($this->folder->getURI().$entries[$key].'.idx'), 
            serialize($page)
          );
        } catch (IOException $e) {
          throw($e);
        }
      }
      
      return TRUE;
    }
 
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setTrace(&$cat) {
      $this->cat= &$cat;
    }

  } 
?>
