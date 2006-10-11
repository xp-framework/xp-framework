<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('ARCHIVE_READ',             0x0000);
  define('ARCHIVE_CREATE',           0x0001);
  define('ARCHIVE_HEADER_SIZE',      0x0100);
  define('ARCHIVE_INDEX_ENTRY_SIZE', 0x0100);

  /**
   * Simple lightweight archive reader. This is a reading-only
   * archive class which can be used to minimize class
   * dependencies.
   * 
   * @see      xp://lang.archive.ArchiveClassLoader
   * @purpose  Lightweight archive reader
   */
  class ArchiveReader extends Object {
    var
      $file     = '',
      $version  = 1;
    
    var
      $_hdl     = NULL,
      $_index   = array();

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($filename) {
      $this->file= $filename;
    }
    
    /**
     * Check whether a given element exists
     *
     * @access  public
     * @param   string id the element's id
     * @return  bool TRUE when the element exists
     */
    function contains($id) {
      return isset($this->_index[$id]);
    }    
    
    /**
     * Get entry (iterative use)
     * <code>
     *   $a= &new Archive(new File('port.cca'));
     *   $a->open(ARCHIVE_READ);
     *   while ($id= $a->getEntry()) {
     *     var_dump($id);
     *   }
     *   $a->close();
     * </code>
     *
     * @access  public
     * @return  string id or FALSE to indicate the pointer is at the end of the list
     */
    function getEntry() {
      $key= key($this->_index);
      next($this->_index);
      return $key;
    }

    /**
     * Rewind archive
     *
     * @access  public
     */
    function rewind() {
      reset($this->_index);
    }
    
    /**
     * Extract a file's contents
     *
     * @access  public
     * @param   string id
     * @return  &string content
     */
    function &extract($id) {
      if (!$this->contains($id)) {
        return FALSE;
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][3]
      );
      
      fseek($this->_hdl, $pos, SEEK_SET);
      $data= fread($this->_hdl, $this->_index[$id][2]);
      return $data;
    }
    
    /**
     * Fetches a stream to the file in the archive
     *
     * @access  public
     * @param   string id
     * @return  &io.Stream
     * @throws  lang.ElementNotFoundException in case the specified id does not exist
     */
    function &getStream($id) {
      if (!$this->contains($id)) {
        return FALSE;
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][3]
      );
      
      // Load the class only at runtime to keep hardcoded dependencies to
      // external (ie. != "lang.") classes at a minimum to not affect
      // core startup time.
      $class= &XPClass::forName('io.EnclosedStream');
      $s= &$class->newInstance($this->file, $pos, $this->_index[$id][2]);
      return $s;
    }
    
    /**
     * Open this archive
     *
     * @access  public
     * @param   int mode default ARCHIVE_READ one of ARCHIVE_READ | ARCHIVE_CREATE
     * @return  bool success
     */
    function open($mode) {
      switch ($mode) {
        case ARCHIVE_READ:      // Load
          $this->_hdl= fopen($this->file, 'rb');
          $header= fread($this->_hdl, ARCHIVE_HEADER_SIZE);
          $data= unpack('a3id/c1version/i1indexsize/a*reserved', $header);
            
          // Check header integrity
          if ('CCA' !== $data['id']) throw(new FormatException(sprintf(
            'Header malformed: "CCA" expected, have "%s"', 
            substr($header, 0, 3)
          )));
          
          // Copy information
          $this->version = $data['version'];
          
          // Read index
          for ($i= 0; $i < $data['indexsize']; $i++) {
            $entry= unpack(
              'a80id/a80filename/a80path/i1size/i1offset/a*reserved', 
              fread($this->_hdl, ARCHIVE_INDEX_ENTRY_SIZE)
            );
            $this->_index[$entry['id']]= array(
              $entry['filename'],
              $entry['path'],
              $entry['size'],
              $entry['offset'],
              NULL              // Will not be read, use extract()
            );
          }
          
          return TRUE;
      }
      
      return FALSE;
    }
    
    /**
     * Close this archive
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return fclose($this->_hdl);
    }
    
    /**
     * Checks whether this archive is open
     *
     * @access  public
     * @param   bool TRUE when the archive file is open
     */
    function isOpen() {
      return is_resource($this->_hdl);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s(version= %s, index size= %d) { %s }',
        $this->getClassName(),
        $this->version,
        sizeof($this->_index),
        xp::stringOf($this->_hdl)
      );
    }
  }
?>
