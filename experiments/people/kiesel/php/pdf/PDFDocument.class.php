<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'PDFPages',
    'PDFCatalogue',
    'PDFResources',
    'PDFInformation',
    'lang.Collection'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PDFDocument extends Object {
    var
      $catalogue=       NULL,     // PDF Catalogue
      $pages=           NULL,     // Root Pages
      $dict=            NULL,     // Dictionary
      $info=            NULL,     // PDF information
      $objects=         NULL,     // all objects
      $objectcount=     0,        // count of objects
      $trailer=         NULL;     // objects being printed in the trailer
    
    var
      $location=        array(),  // location of objects in final file
      $position=        0;        // Current position in stream

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
    
      // Initializer counters + collections
      $this->objectcount= 0;
      $this->objects= &Collection::forClass('PDFObject');
      $this->trailer= &Collection::forClass('PDFObject');
      
      // Create root and pages
      $this->pages= &new PDFPages(++$this->objectcount);
      $this->trailer->add($this->pages);
      
      $this->catalogue= &new PDFCatalogue(++$this->objectcount);
      $this->catalogue->setRootPages($this->pages);
      $this->trailer->add($this->catalogue);
      
      $this->resources= &new PDFResources(++$this->objectcount);
      $this->trailer->add($this->resources);
      
      $this->info= &new PDFInformation(++$this->objectcount);
      $this->info->setProducer('XP Framework $Id$');
      $this->objects->add($this->info);
    }
    
    function output(&$stream) {
      $this->position= 0;
      $this->_outputHeader($stream);
      $this->_outputObjects($stream);
      $this->_outputTrailer($stream);
    }

    function _outputHeader(&$stream) {
      $l= $stream->writeLine('%PDF-1.3');
      $l+= $stream->writeLine('%'.chr(0xBB).chr(0xBC).chr(0xBD).chr(0xBE));
      $this->position+= $l;
    }
    
    function _outputObjects(&$stream) {
      // Output all objects
      for ($i= 0; $i < $this->objects->size(); $i++) {
        $object= &$this->objects->get($i);
        
        // Write object to stream and remember its position
        $len= $object->output($stream);
        $this->location[$object->getNumber()]= $this->position;
        $this->position+= $len;;
      }
    }
    
    function _outputTrailer(&$stream) {
    
      // Write the trailer objects
      for ($i= 0; $i < $this->trailer->size(); $i++) {
        $object= &$this->trailer->get($i);
        
        // Write object to stream and remember its position
        $this->location[$object->getNumber()]= $this->position;
        $len= $object->output($stream);
        $this->position+= $len;
      }
      $this->position+= $this->_outputXref($stream);
      $this->trailer->clear();
      
      // Write the trailer itself
      $this->position+= (
        $stream->writeLine('trailer') +
        $stream->writeLine('<<') +
        $stream->writeLine('/Size '.($this->objectcount + 1)) +
        $stream->writeLine('/Root '.$this->catalogue->getReference()) +
        $stream->writeLine('/Info '.$this->info->getReference()) +
        $stream->writeLine('>>') + 
        $stream->writeLine('startxref') +
        $stream->writeLine($this->xrefpos) +
        $stream->writeLine('%%EOF')
      );
    }
    
    function _outputXref(&$stream) {
      // Remember location of xref
      $this->xrefpos= $this->position;
      
      $xref= 
        "xref\n".
        "0 ".($this->objectcount + 1)."\n".
        "0000000000 65535 f \n";
      
      foreach ($this->location as $number => $position) {
        $xref.= sprintf("%010d 00000 n \n", $position);
      }
      
      return $stream->write($xref);
    }
  }
?>
