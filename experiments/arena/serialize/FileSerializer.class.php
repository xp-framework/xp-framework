<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Serializer', 'io.File');

  /**
   * Serializer that writes to files
   *
   * @purpose  Serializer
   */
  class FileSerializer extends Serializer {
    protected
      $file  = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   io.File file
     */
    public function __construct(File $file) {
      $this->file= $file;
    }
  
    /**
     * Serialize an object
     *
     * @access  public
     * @param   &io.Serializable object
     */
     public function serialize(Serializable $object) {
       $this->file->open(FILE_MODE_WRITE);
       $this->file->write($this->representationOf($object));
       $this->file->close();
     }
  }
?>
