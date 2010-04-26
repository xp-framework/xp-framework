<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.ElementNotFoundException', 'io.EncapsedStream');

  define('ARCHIVE_READ',             0x0000);
  define('ARCHIVE_CREATE',           0x0001);
  define('ARCHIVE_HEADER_SIZE',      0x0100);
  define('ARCHIVE_INDEX_ENTRY_SIZE', 0x0100);

  /**
   * Archives contain a collection of classes.
   *
   * Usage example (Creating):
   * <code>
   *   $a= new Archive(new File('soap.xar'));
   *   $a->open(ARCHIVE_CREATE);
   *   $a->addFile(
   *     'webservices/soap/SOAPMessage.class.php'
   *     new File($path, 'xml/soap/SOAPMessage.class.php')
   *   );
   *   $a->create();
   * </code>
   *
   * Usage example (Extracting):
   * <code>
   *   $a= new Archive(new File('soap.xar'));
   *   $bytes= $a->extract('webservices/soap/SOAPMessage.class.php');
   * </code>
   * 
   * @test  xp://net.xp_framework.unittest.archive.ArchiveV1Test
   * @test  xp://net.xp_framework.unittest.archive.ArchiveV2Test
   * @test  xp://net.xp_framework.unittest.core.ArchiveClassLoaderTest
   * @see   http://java.sun.com/javase/6/docs/api/java/util/jar/package-summary.html
   */
  class Archive extends Object {
    public
      $file     = NULL,
      $version  = 2;
    
    public
      $_index  = array();
        
    /**
     * Constructor
     *
     * @param   io.File file
     */
    public function __construct($file) {
      $this->file= $file;
    }
    
    /**
     * Get URI
     *
     * @return  string uri
     */
    public function getURI() {
      return $this->file->getURI();
    }
    
    /**
     * Add a file
     *
     * @param   io.File file
     * @param   string id the id under which this entry will be located
     * @return  bool success
     * @deprecated Use addFile() instead
     */
    public function add($file, $id) {
      $file->open(FILE_MODE_READ);
      $data= $file->read($file->size());
      $file->close();

      $this->_index[$id]= array(strlen($data), -1, $data);
      return TRUE;
    }
    
    /**
     * Add a file by its bytes
     *
     * @param   string id the id under which this entry will be located
     * @param   string path
     * @param   string filename
     * @param   string bytes
     * @deprecated Use addBytes() instead
     */
    public function addFileBytes($id, $path, $filename, $bytes) {
      $this->_index[$id]= array(strlen($bytes), -1, $bytes);
    }


    /**
     * Add a file by its bytes
     *
     * @param   string id the id under which this entry will be located
     * @param   string bytes
     */
    public function addBytes($id, $bytes) {
      $this->_index[$id]= array(strlen($bytes), -1, $bytes);
    }

    /**
     * Add a file
     *
     * @param   string id the id under which this entry will be located
     * @param   io.File file
     */
    public function addFile($id, $file) {
      $file->open(FILE_MODE_READ);
      $bytes= $file->read($file->size());
      $file->close();

      $this->_index[$id]= array(strlen($bytes), -1, $bytes);
    }
    
    /**
     * Create CCA archive
     *
     * @return  bool success
     */
    public function create() {
      $this->file->truncate();
      $this->file->write(pack(
        'a3c1V1a248', 
        'CCA',
        $this->version,
        sizeof(array_keys($this->_index)),
        "\0"                  // Reserved for future use
      ));

      // Write index
      $offset= 0;
      foreach (array_keys($this->_index) as $id) {
        $this->file->write(pack(
          'a240V1V1a8',
          $id,
          $this->_index[$id][0],
          $offset,
          "\0"                   // Reserved for future use
        ));
        $offset+= $this->_index[$id][0];
      }

      // Write files
      foreach (array_keys($this->_index) as $id) {
        $this->file->write($this->_index[$id][2]);
      }

      $this->file->close();
      return TRUE;
    }
    
    /**
     * Check whether a given element exists
     *
     * @param   string id the element's id
     * @return  bool TRUE when the element exists
     */
    public function contains($id) {
      return isset($this->_index[$id]);
    }
    
    /**
     * Get entry (iterative use)
     * <code>
     *   $a= new Archive(new File('port.xar'));
     *   $a->open(ARCHIVE_READ);
     *   while ($id= $a->getEntry()) {
     *     var_dump($id);
     *   }
     *   $a->close();
     * </code>
     *
     * @return  string id or FALSE to indicate the pointer is at the end of the list
     */
    public function getEntry() {
      $key= key($this->_index);
      next($this->_index);
      return $key;
    }

    /**
     * Rewind archive
     *
     */
    public function rewind() {
      reset($this->_index);
    }
    
    /**
     * Extract a file's contents
     *
     * @param   string id
     * @return  string content
     * @throws  lang.ElementNotFoundException in case the specified id does not exist
     */
    public function extract($id) {
      if (!$this->contains($id)) {
        throw new ElementNotFoundException('Element "'.$id.'" not contained in this archive');
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][1]
      );
      
      try {
        $this->file->isOpen() || $this->file->open(FILE_MODE_READ);
        $this->file->seek($pos, SEEK_SET);
        $data= $this->file->read($this->_index[$id][0]);
      } catch (XPException $e) {
        throw new ElementNotFoundException('Element "'.$id.'" cannot be read: '.$e->getMessage());
      }
      
      return $data;
    }
    
    /**
     * Fetches a stream to the file in the archive
     *
     * @param   string id
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the specified id does not exist
     */
    public function getStream($id) {
      if (!$this->contains($id)) {
        throw new ElementNotFoundException('Element "'.$id.'" not contained in this archive');
      }

      // Calculate starting position      
      $pos= (
        ARCHIVE_HEADER_SIZE + 
        sizeof(array_keys($this->_index)) * ARCHIVE_INDEX_ENTRY_SIZE +
        $this->_index[$id][1]
      );
      
      return new EncapsedStream($this->file, $pos, $this->_index[$id][0]);
    }
    
    /**
     * Open this archive
     *
     * @param   int mode default ARCHIVE_READ one of ARCHIVE_READ | ARCHIVE_CREATE
     * @return  bool success
     * @throws  lang.IllegalArgumentException in case an illegal mode was specified
     * @throws  lang.FormatException in case the header is malformed
     */
    public function open($mode) {
      static $unpack= array(
        1 => 'a80id/a80*filename/a80*path/V1size/V1offset/a*reserved',
        2 => 'a240id/V1size/V1offset/a*reserved'
      );
      
      switch ($mode) {
        case ARCHIVE_READ:      // Load
          $this->file->open(FILE_MODE_READ);

          // Read header
          $header= $this->file->read(ARCHIVE_HEADER_SIZE);
          $data= unpack('a3id/c1version/V1indexsize/a*reserved', $header);

          // Check header integrity
          if ('CCA' !== $data['id']) throw new FormatException(sprintf(
            'Header malformed: "CCA" expected, have "%s"', 
            substr($header, 0, 3)
          ));
          
          // Copy information
          $this->version= $data['version'];
          
          // Read index
          for ($i= 0; $i < $data['indexsize']; $i++) {
            $entry= unpack($unpack[$this->version], $this->file->read(ARCHIVE_INDEX_ENTRY_SIZE));
            $this->_index[$entry['id']]= array($entry['size'], $entry['offset'], NULL);
          }
          return TRUE;
          
        case ARCHIVE_CREATE:    // Create
          return $this->file->open(FILE_MODE_WRITE);
          
      }
      
      throw new IllegalArgumentException('Mode '.$mode.' not recognized');
    }
    
    /**
     * Close this archive
     *
     * @return  bool success
     */
    public function close() {
      return $this->file->close();
    }
    
    /**
     * Checks whether this archive is open
     *
     * @return  bool TRUE when the archive file is open
     */
    public function isOpen() {
      return $this->file->isOpen();
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
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
     */
    public function __destruct() {
      if ($this->isOpen()) $this->close();
    }
  }
?>
