<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.ElementNotFoundException', 'io.EncapsedStream');

  /**
   * Archives contain a collection of classes.
   *
   * Usage example (Creating):
   * <code>
   *   $a= &new Archive(new File('soap.cca'));
   *   try(); {
   *     $a->open(ARCHIVE_CREATE);
   *     $a->add(
   *       new File(SKELETON_PATH.'xml/soap/SOAPMessage.class.php'), 
   *       'xml.soap.SOAPMessage'
   *     );
   *     $a->add(
   *       new File(SKELETON_PATH.'xml/soap/SOAPClient.class.php'),
   *       'xml.soap.SOAPClient'
   *     );
   *     $a->create();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *   }
   * </code>
   *
   * Usage example (Extracting):
   * <code>
   *   $a= &new Archive(new File('soap.cca'));                               
   *   try(); {                                                              
   *     $a->open(ARCHIVE_READ);                                             
   *     $c= array(                                                          
   *       'xml.soap.SOAPMessage' => $a->extract('xml.soap.SOAPMessage'),    
   *       'xml.soap.SOAPClient'  => $a->extract('xml.soap.SOAPClient')      
   *     );                                                                  
   *   } if (catch('Exception', $e)) {                                       
   *     $e->printStackTrace();                                              
   *   }                                                                     
   *   var_dump($c);                                                         
   * </code>
   * 
   * @purpose  Provide an archiving
   * @see      http://java.sun.com/j2se/1.4/docs/api/java/util/jar/package-summary.html
   */
  class Archive extends Object {
    var
      $file     = NULL,
      $version  = 1;
    
    var
      $_index  = array();
        
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
      $this->file= &$file;
      
    }
    
    /**
     * Add a file
     *
     * @access  public
     * @param   &io.File file
     * @param   string id the id under which this entry will be located
     * @return  bool success
     */
    function add(&$file, $id) {
      try(); {
        $file->open(FILE_MODE_READ);
        $data= $file->read($file->size());
        $file->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      $this->_index[$id]= array(
        $file->filename,
        $file->path,
        strlen($data),
        -1,                 // Will be calculated by create()
        $data
      );
      return TRUE;
    }
    
    /**
     * Add a file by its bytes
     *
     * @access  public
     * @param   string id the id under which this entry will be located
     * @param   string path
     * @param   string filename
     * @param   string bytes
     */
    function addFileBytes($id, $path, $filename, $bytes) {
      $this->_index[$id]= array(
        $filename,
        $path,
        strlen($bytes),
        -1,                 // Will be calculated by create()
        $bytes
      );
    }
    
    /**
     * Create CCA archive
     *
     * @access  public
     * @return  bool success
     */
    function create() {
      try(); {
        $this->file->truncate();
        $this->file->write(pack(
          'a3c1i1a248', 
          'CCA',
          $this->version,
          sizeof(array_keys($this->_index)),
          "\0"                  // Reserved for future use
        ));
        
        // Write index
        $offset= 0;
        foreach (array_keys($this->_index) as $id) {
          $this->file->write(pack(
            'a80a80a80i1i1a8',
            $id,
            $this->_index[$id][0],
            $this->_index[$id][1],
            $this->_index[$id][2],
            $offset,
            "\0"                   // Reserved for future use
          ));
          $offset+= $this->_index[$id][2];
        }
        
        // Write files
        foreach (array_keys($this->_index) as $id) {
          $this->file->write($this->_index[$id][4]);
        }
        
        $this->file->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return TRUE;
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
     * @throws  lang.ElementNotFoundException in case the specified id does not exist
     */
    function &extract($id) {
      if (!$this->contains($id)) {
        return throw(new ElementNotFoundException('Element "'.$id.'" not contained in this archive'));
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][3]
      );
      
      try(); {
        $this->file->isOpen() || $this->file->open(FILE_MODE_READ);
        $this->file->seek($pos, SEEK_SET);
        $data= $this->file->read($this->_index[$id][2]);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
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
        return throw(new ElementNotFoundException('Element "'.$id.'" not contained in this archive'));
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][3]
      );
      
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
     * @throws  lang.IllegalArgumentException in case an illegal mode was specified
     * @throws  lang.FormatException in case the header is malformed
     */
    function open($mode) {
      switch ($mode) {
        case ARCHIVE_READ:      // Load
          try(); {
            $this->file->open(FILE_MODE_READ);
            
            // Read header
            $header= $this->file->read(ARCHIVE_HEADER_SIZE);
            $data= unpack('a3id/c1version/i1indexsize/a*reserved', $header);
            
            // Check header integrity
            if ('CCA' !== $data['id']) throw(new FormatException(sprintf(
              'Header malformed: "CCA" expected, have "%s"', 
              substr($header, 0, 3)
            )));
          } if (catch('Exception', $e)) {
            return throw($e);
          }
          
          // Copy information
          $this->version = $data['version'];
          
          // Read index
          for ($i= 0; $i < $data['indexsize']; $i++) {
            $entry= unpack(
              'a80id/a80filename/a80path/i1size/i1offset/a*reserved', 
              $this->file->read(ARCHIVE_INDEX_ENTRY_SIZE)
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
          
        case ARCHIVE_CREATE:    // Create
          return $this->file->open(FILE_MODE_WRITE);
          
      }
      
      return throw(new IllegalArgumentException('Mode '.$mode.' not recognized'));
    }
    
    /**
     * Close this archive
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return $this->file->close();
    }
    
    /**
     * Checks whether this archive is open
     *
     * @access  public
     * @param   bool TRUE when the archive file is open
     */
    function isOpen() {
      return $this->file->isOpen();
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
        xp::stringOf($this->file)
      );
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      if ($this->isOpen()) $this->close();
    }
  }
?>
