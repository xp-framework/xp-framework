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
     * @param   int entriesPerPage
     */
    public function setEntriesPerPage($entriesPerPage) {
      $this->entriesPerPage= $entriesPerPage;
    }

    /**
     * Get entriesPerPage
     *
     * @return  int
     */
    public function getEntriesPerPage() {
      return $this->entriesPerPage;
    }
    
    /**
     * Returns a IndexCreator for a given folder
     *
     * @param   &io.Folder folder
     * @return  &de.thekid.dialog.io.IndexCreator
     */
    public static function forFolder($folder) {
      $i= new IndexCreator();
      $i->folder= $folder;
      return $i;
    }
    
    /**
     * Regenerate
     *
     * @return  bool success
     * @throws  io.IOException
     */
    public function regenerate() {
      $entries= array();
      for ($i= new FilteredFolderIterator($this->folder, '/\.dat$/'); $i->hasNext(); ) {
        $entry= $i->next();
        $file= new File($entry);
        $data= unserialize(FileUtil::getContents($file));

        $date= $data->getDate();
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

        FileUtil::setContents(
          new File($this->folder->getURI().'page_'.($i / $this->entriesPerPage).'.idx'), 
          serialize(array(
            'total'   => $s, 
            'perpage' => $this->entriesPerPage,
            'entries' => array_slice($entries, $i, $this->entriesPerPage)
          ))
        );
      }
      
      // ...by album name, for album -> page lookup
      foreach (array_keys($entries) as $i => $key) {
        $page= intval(floor($i / $this->entriesPerPage));
        $this->cat && $this->cat->debugf(
          '---> Element %s is on page %d',
          $entries[$key],
          $page
        );
        
        FileUtil::setContents(
          new File($this->folder->getURI().$entries[$key].'.idx'), 
          serialize($page)
        );
      }
      
      // ...by years
      $bydate= array();
      foreach (array_keys($entries) as $key) {
        sscanf($key, '%4d%2d', $year, $month);
        @$bydate[$year][$month][]= $entries[$key];
      }
      foreach ($bydate as $year => $bymonth) {
        $this->cat && $this->cat->debugf(
          '---> For %s: %s',
          $year,
          xp::stringOf($bymonth)
        );
        FileUtil::setContents(
          new File($this->folder->getURI().'bydate_'.$year.'.idx'), 
          serialize($bymonth)
        );
      }
      FileUtil::setContents(
        new File($this->folder->getURI().'bydate.idx'), 
        serialize(array_keys($bydate))
      );
      
      return TRUE;
    }
 
    /**
     * Set a trace for debugging
     *
     * @param   &util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

  } 
?>
