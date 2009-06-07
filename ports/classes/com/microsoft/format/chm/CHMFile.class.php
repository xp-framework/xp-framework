<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'com.microsoft.format.chm.CHMHeader',
    'com.microsoft.format.chm.CHMDirectory'
  );

  // Languages  
  define('CHM_LANG_ENGLISH',    0x0409);
  define('CHM_LANG_GERMAN',     0x0407);
  
  // Chunk types
  define('CHM_CHUNK_LISTING',   0x0000);
  define('CHM_CHUNK_INDEX',     0x0001);
  
  // Well known identifier for namelist
  define('CHM_FILE_NAMELIST',   '::DataSpace/NameList');
  
  // Compressed / uncompressed identifiers
  define('CHM_COMPRESSED',      'MSCompressed');
  define('CHM_UNCOMPRESSED',    'Uncompressed');

  /**
   * CHM (Compile HTML) reader
   *
   * Example (extract CHM header):
   * <code>
   *   uses('com.microsoft.format.chm.CHMFile', 'io.File');
   *
   *   $f= new CHMFile(new File('file.chm'));
   *   try(); {
   *     $f->open();
   *     $header= $f->getHeader();
   *     $dir= $f->getDirectory();
   *     $f->close();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   var_export($header);
   *   var_export($dir);
   * </code>
   *
   * @see      http://www.speakeasy.org/~russotto/chm/chmformat.html
   * @purpose  CHM
   * @experimental
   */
  class CHMFile extends Object {
    public
      $stream           = NULL,
      $header           = NULL,
      $directory        = NULL;
      
    public
      $_headeroffset    = 0,
      $_diroffset       = 0;
    
    /**
     * Constructor
     *
     * @param   io.Stream stream
     */  
    public function __construct($stream) {
      $this->stream= $stream;
    }

    /**
     * Create a GUID from an array
     *
     * @param   array g
     * @return  string guid
     */
    protected function _guid($g) {
      return vsprintf('{%08X-%04X-%04X-%02X%02X-%02X%02X-%02X%02X-%02X%02X}', $g);
    }

    /**
     * Retrieve a substring and set the pointer to after the 
     * substring's length,
     *
     * @param   string str
     * @param   &int p
     * @param   int len
     * @return  string str
     */
    protected function _substr($str, &$p, $len) {
      $str= substr($str, ++$p, $len);
      $p+= $len;
      return $str;
    }

    /**
     * Get an int encoded within a string and set the pointer to
     * after the position
     *
     * @param   string str
     * @param   &int p
     * @return  int
     */
    protected function _int($str, $p) {
      $r= 0;
      while (ord($str{$p}) & 0x80) {
        $r= ($r << 7) | (ord($str{$p++}) & 0x7F);
      }
      $r= ($r << 7) | ord($str{$p++});
      return $r;
    }
    
    /**
     * Read directory
     *
     * @param   int length
     * @return  array entries
     */
    protected function _dir($length, $qref, $format) {
      $str= $this->stream->read($length- $qref);
      $pos= 0;
      $max= strlen($str);
      do {
        $len= ord($str{$pos});
        $name= $this->_substr($str, $pos, $len);
        switch ($format) {
          case CHM_CHUNK_LISTING: 
            $entries[$name]= array(
              'name'    => $name,
              'section' => $this->_int($str, $pos),
              'offset'    => $this->_int($str, $pos),
              'length'    => $this->_int($str, $pos)
            );
            break;

          case CHM_CHUNK_INDEX:
            $entries[$name]= array(
              'name'        => $name,
              'index'        => $this->_int($str, $pos)
            );
            break;
        }
      } while ($pos < $max);
      $this->stream->seek($qref, SEEK_CUR);
      
      return $entries;
    }

    /**
     * Open file
     *
     * @return  bool success
     */
    public function open() {
      return $this->stream->open(FILE_MODE_READ);
    }
    
    /**
     * Close file
     *
     * @return  bool success
     */
    public function close() {
      return $this->stream->open(FILE_MODE_READ);
    }
    
    /**
     * Retrieve CHM header; extracts it if necessary
     *
     * @return  com.microsoft.format.chm.CHMHeader
     * @throws  lang.FormatException if the identifier is not correct
     */
    public function getHeader() {
      if (!empty($this->header)) {
        $this->stream->seek($this->_headeroffset, SEEK_SET);
        return $this->header;
      }
      
      $this->stream->seek(0x00, SEEK_SET);
      if (CHM_HEADER_IDENTIFIER !== ($id= $this->stream->read(4))) {
        throw(new FormatException(
          '"'.addcslashes($id, "\0..\17").'" is not a correct identifier, expecting "ITSF"'
        ));
      }
      
      // Create header
      $this->header= new CHMHeader(unpack(
        'a4identifier/Lversion/Llength/Lunknown/Ltime/Llang', 
        $id.$this->stream->read(0x14)
      ));
      
      // Always {7C01FD10-7BAA-11D0-9E0C-00A0-C922-E6EC}
      $this->header->setGuid1($this->_guid(unpack(
        'Lguid1/v2guid2/C8guid3',
        $this->stream->read(0x10)
      )));
      
      // Always {7C01FD11-7BAA-11D0-9E0C-00A0-C922-E6EC}
      $this->header->setGuid2($this->_guid(unpack(
        'Lguid1/v2guid2/C8guid3',
        $this->stream->read(0x10)
      )));

      // Section table
      $this->header->setSection(unpack(
        'Loffset/Llength',
        $this->stream->read(0x16)
      ));

      // Content_offset
      if (3 == $this->header->getVersion()) {
        $this->stream->seek(0x58, SEEK_SET);
        $this->header->setContent_offset(array_shift(array_values(unpack('L', $this->stream->read(0x20)))));
      }    

      $this->_headeroffset= $this->stream->tell();
      return $this->header;    
    }
    
    /**
     * Retrieve CHM directory; extracts it if necessary
     *
     * @return  &com.microsoft.format.chm.CHMDirectory
     * @throws  lang.FormatException if the identifier is not correct
     */ 
    public function getDirectory() {
      $this->getHeader();       // We need this so the file pointer position is correct
      if (!empty($this->directory)) {
        $this->stream->seek($this->_diroffset, SEEK_SET);
        return $this->directory;
      }

      if (CHM_DIRECTORY_IDENTIFIER !== ($id= $this->stream->read(4))) {
        throw new FormatException(
          '"'.addcslashes($id, "\0..\17").'" is not a correct identifier, expecting "ITSP"'
        );
      }
      
      // Create directory object
      $directory= new CHMDirectory(unpack(
        'a4identifier/Lversion/Llength/Lunknown/Lchunk_size/Ldensity/Ldepth/Lrootindex_chunk/Lfirst_pmgl/Llast_pmgl/Lunknown/Lnum_chunks/Llang',
        $id.$this->stream->read(0x30)
      ));
      
      // Always {5D02926A-212E-11D0-9DF9-00A0-C922-E6EC}
      $directory->setGuid($this->_guid(unpack(
        'Lguid1/v2guid2/C8guid3',
        $this->stream->read(0x10)
      )));
      
      // Don't know what this is, read it anyway
      $directory->setExt(unpack(
        'Llength/L3unknown',
        $this->stream->read(0x10)
      ));
      
      // Read chunks
      $listing= TRUE;
      for ($i= 0; $i < $directory->getNum_chunks(); $i++) {
        $chunk= array();
        if ($listing) {    // Listing chunks
          $chunk['type']= CHM_CHUNK_LISTING;
          $chunk['listing']= unpack(
            'a4identifier/Lquickref_length/Lunused/Lchunk_prev/Lchunk_next',
            $this->stream->read(0x14)
          );
          $chunk['n']= 1 + (1 << $directory->getDensity());
          $chunk['entries']= $this->_dir(
            $directory->getChunk_size()- 0x14, 
            $chunk['listing']['quickref_length'],
            CHM_CHUNK_LISTING
          );

          // Next chunk is index chunk
          if (-1 == $chunk['listing']['chunk_next']) $listing= FALSE;
        } else {
          $chunk['type']= CHM_CHUNK_INDEX;

          $chunk['index']= unpack(
            'a4identifier/Llength',
            $this->stream->read(0x8)
          );

          $chunk['entries']= $this->_dir(
            $directory->getChunk_size()- 0x8, 
            $chunk['index']['length'],
            CHM_CHUNK_INDEX
          );
        }
        $directory->addChunk($chunk);
      }
      
      $this->_diroffset= $this->stream->tell();
      return $directory;
    }
  }
?>
