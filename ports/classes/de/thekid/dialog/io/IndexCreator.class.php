<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'de.thekid.dialog.io.FilteredFolderIterator'
  );

  /**
   * Creates indexes
   *
   * @purpose  Helper class
   */
  class IndexCreator extends Object {
    var
      $cat            = NULL,
      $folder         = NULL,
      $entriesPerPage = 5;

    /**
     * Set entriesPerPage
     *
     * @access  public
     * @param   int entriesPerPage
     */
    function setEntriesPerPage($entriesPerPage) {
      $this->entriesPerPage= $entriesPerPage;
    }

    /**
     * Get entriesPerPage
     *
     * @access  public
     * @return  int
     */
    function getEntriesPerPage() {
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
    function &forFolder(&$folder) {
      $i= &new IndexCreator();
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
    function regenerate() {
      $entries= array();
      for ($i= &new FilteredFolderIterator($this->folder, '/\.dat$/'); $i->hasNext(); ) {
        $entry= $i->next();
        $entries[filemtime($this->folder->getURI().$entry).$entry]= basename($entry, '.dat');
      }
      $this->folder->close();
      krsort($entries);
      $this->cat && $this->cat->debug($entries);

      // ...by pages. The index "page_0" can be used for the home page
      for ($i= 0, $s= sizeof($entries); $i < $s; $i+= $this->entriesPerPage) {
        $this->cat && $this->cat->debugf(
          '---> Generating index for album #%d - #%d', 
          $i, 
          $i+ $this->entriesPerPage
        );

        try(); {
          FileUtil::setContents(
            new File($this->folder->getURI().'page_'.($i / $this->entriesPerPage).'.idx'), 
            serialize(array(
              'total'   => $s, 
              'perpage' => $this->entriesPerPage,
              'entries' => array_slice($entries, $i, $this->entriesPerPage)
            ))
          );
        } if (catch('IOException', $e)) {
          return throw($e);
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
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }

  } implements(__FILE__, 'util.log.Traceable');
?>


