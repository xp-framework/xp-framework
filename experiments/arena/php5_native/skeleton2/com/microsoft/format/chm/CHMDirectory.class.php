<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Header identifier
  define('CHM_DIRECTORY_IDENTIFIER',   'ITSP');

  /**
   * CHM directory
   *
   * @see      xp://com.microsoft.format.chm.CHMFile#getDirectory
   * @purpose  CHM file class
   * @experimental
   */
  class CHMDirectory extends Object {
    public
      $identifier       = CHM_DIRECTORY_IDENTIFIER,
      $version          = 0,
      $length           = 0,
      $unknown          = 0,
      $chunk_size       = 0,
      $density          = 0,
      $depth            = 0,
      $rootindex_chunk  = 0,
      $first_pmgl       = 0,
      $last_pmgl        = 0,
      $num_chunks       = 0,
      $lang             = 0,
      $guid             = '',
      $ext              = array(),
      $chunks           = array();
      
    /**
     * Adds a chunk
     *
     * @access  public
     * @param   string chunk
     */
    public function addChunk($chunk) {
      $this->chunks[]= $chunk;
    }
    
    /**
     * Get's the ith chunk.
     *
     * @access  public
     * @param   int i
     * @return  string
     */
    public function getChunk($i) {
      return $this->chunks[$i];
    }

    /**
     * Set Identifier
     *
     * @access  public
     * @param   string identifier
     */
    public function setIdentifier($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Get Identifier
     *
     * @access  public
     * @return  string
     */
    public function getIdentifier() {
      return $this->identifier;
    }

    /**
     * Set Version
     *
     * @access  public
     * @param   int version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @access  public
     * @return  int
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Set Length
     *
     * @access  public
     * @param   int length
     */
    public function setLength($length) {
      $this->length= $length;
    }

    /**
     * Get Length
     *
     * @access  public
     * @return  int
     */
    public function getLength() {
      return $this->length;
    }

    /**
     * Set Chunk_size
     *
     * @access  public
     * @param   int chunk_size
     */
    public function setChunk_size($chunk_size) {
      $this->chunk_size= $chunk_size;
    }

    /**
     * Get Chunk_size
     *
     * @access  public
     * @return  int
     */
    public function getChunk_size() {
      return $this->chunk_size;
    }

    /**
     * Set Density
     *
     * @access  public
     * @param   int density
     */
    public function setDensity($density) {
      $this->density= $density;
    }

    /**
     * Get Density
     *
     * @access  public
     * @return  int
     */
    public function getDensity() {
      return $this->density;
    }

    /**
     * Set Depth
     *
     * @access  public
     * @param   int depth
     */
    public function setDepth($depth) {
      $this->depth= $depth;
    }

    /**
     * Get Depth
     *
     * @access  public
     * @return  int
     */
    public function getDepth() {
      return $this->depth;
    }

    /**
     * Set Rootindex_chunk
     *
     * @access  public
     * @param   int rootindex_chunk
     */
    public function setRootindex_chunk($rootindex_chunk) {
      $this->rootindex_chunk= $rootindex_chunk;
    }

    /**
     * Get Rootindex_chunk
     *
     * @access  public
     * @return  int
     */
    public function getRootindex_chunk() {
      return $this->rootindex_chunk;
    }

    /**
     * Set First_pmgl
     *
     * @access  public
     * @param   int first_pmgl
     */
    public function setFirst_pmgl($first_pmgl) {
      $this->first_pmgl= $first_pmgl;
    }

    /**
     * Get First_pmgl
     *
     * @access  public
     * @return  int
     */
    public function getFirst_pmgl() {
      return $this->first_pmgl;
    }

    /**
     * Set Last_pmgl
     *
     * @access  public
     * @param   int last_pmgl
     */
    public function setLast_pmgl($last_pmgl) {
      $this->last_pmgl= $last_pmgl;
    }

    /**
     * Get Last_pmgl
     *
     * @access  public
     * @return  int
     */
    public function getLast_pmgl() {
      return $this->last_pmgl;
    }

    /**
     * Set Num_chunks
     *
     * @access  public
     * @param   int num_chunks
     */
    public function setNum_chunks($num_chunks) {
      $this->num_chunks= $num_chunks;
    }

    /**
     * Get Num_chunks
     *
     * @access  public
     * @return  int
     */
    public function getNum_chunks() {
      return $this->num_chunks;
    }

    /**
     * Set Lang
     *
     * @access  public
     * @param   int lang
     */
    public function setLang($lang) {
      $this->lang= $lang;
    }

    /**
     * Get Lang
     *
     * @access  public
     * @return  int
     */
    public function getLang() {
      return $this->lang;
    }

    /**
     * Set Guid
     *
     * @access  public
     * @param   string guid
     */
    public function setGuid($guid) {
      $this->guid= $guid;
    }

    /**
     * Get Guid
     *
     * @access  public
     * @return  string
     */
    public function getGuid() {
      return $this->guid;
    }

    /**
     * Set Ext
     *
     * @access  public
     * @param   mixed[] ext
     */
    public function setExt($ext) {
      $this->ext= $ext;
    }

    /**
     * Get Ext
     *
     * @access  public
     * @return  mixed[]
     */
    public function getExt() {
      return $this->ext;
    }
  }
?>
