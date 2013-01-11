<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.io.XmlWriter', 'io.streams.OutputStream');

  /**
   * Outputs XML to an output stream.
   *
   * @test    xp://net.xp_framework.unittest.xml.io.XmlStreamWriterTest
   */
  class XmlStreamWriter extends xml·io·XmlWriter {
    protected $stack= array();
    protected $stream= NULL;
    
    /**
     * Creates a new XML writer
     *
     * @param   io.streams.OutputStream stream
     */
    public function __construct(OutputStream $stream) {
      $this->stream= $stream;
    }
  
    /**
     * Start writing a document
     *
     * @param   string version default "1.0"
     * @param   string encoding defaults to XP default encoding
     * @param   bool standalone default FALSE
     */
    public function startDocument($version= '1.0', $encoding= xp::ENCODING, $standalone= FALSE) {
      $this->stream->write(sprintf(
        '<?xml version="%s" encoding="%s"%s?>',
        $version,
        $encoding,
        $standalone ? ' standalone="yes"' : ''
      ));
    }

    /**
     * Close document. Will close all opened tags.
     *
     */
    public function closeDocument() {
      while ($this->stack) {
        $this->stream->write(array_pop($this->stack));
      }
    }
    
    /**
     * Open an element
     *
     * @param   string name
     * @param   [:string] attributes
     */
    public function startElement($name, $attributes= array()) {
      $this->stream->write('<'.$name);
      foreach ($attributes as $key => $value) {
        $this->stream->write(' '.$key.'="'.htmlspecialchars($value).'"');
      }
      $this->stream->write('>');
      $this->stack[]= '</'.$name.'>';
    }
    
    /**
     * Closes an opening tag. 
     *
     * Throws an ISE for incorrectly nested calls, e.g. 
     * <code>
     *   $w->startElement('name');
     *   $w->startComment();
     *   $w->endElement();
     * </pre>
     *
     * @param   string what
     * @throws  lang.IllegalStateException
     */
    protected function _close($what) {
      if (0 !== strncmp($what, $p= array_pop($this->stack), strlen($what))) {
        throw new IllegalStateException('Incorrect nesting, expecting '.$what.'..., have '.$p);
      }
      $this->stream->write($p);
    }
    
    /**
     * Close an element previously opened with startElement()
     *
     */
    public function closeElement() {
      $this->_close('</');
    }

    /**
     * Start a comment
     *
     */
    public function startComment() {
      $this->stream->write('<!--');
      $this->stack[]= '-->';
    }
    
    /**
     * Close a comment
     *
     */
    public function closeComment() {
      $this->_close('-->');
    }

    /**
     * Start a CDATA section
     *
     */
    public function startCData() {
      $this->stream->write('<![CDATA[');
      $this->stack[]= ']]>';
    }
    
    /**
     * Close a CDATA section
     *
     */
    public function closeCData() {
      $this->_close(']]>');
    }

    /**
     * Start a processing instruction.
     *
     * @param   string target
     */
    public function startPI($target) {
      $this->stream->write('<?'.$target.' ');
      $this->stack[]= '?>';
    }
    
    /**
     * Close a processing instruction
     *
     */
    public function closePI() {
      $this->_close('?>');
    }

    /**
     * Writes text
     *
     * @param   string content
     */
    public function writeText($content) {
      $this->stream->write(htmlspecialchars($content));
    }

    /**
     * Writes an entire CDATA section
     *
     * @param   string content
     */
    public function writeCData($content) {
      $this->stream->write('<![CDATA['.$content.']]>');
    }

    /**
     * Write raw string. No escaping or checks are performed on the content,
     * XML conformity of the output document is not guaranteed - it depends
     * on the given input.
     *
     * @param   string content
     */
    public function writeRaw($content) {
      $this->stream->write($content);
    }

    /**
     * Writes a comment
     *
     * @param   string content
     */
    public function writeComment($content) {
      $this->stream->write('<!--'.$content.'-->');
    }

    /**
     * Writes a processing instruction
     *
     * @param   string target
     * @param   var content either a string or a map with attributes
     */
    public function writePI($target, $content) {
      $this->stream->write('<?'.$target);
      if (is_array($content)) {
        foreach ($content as $key => $value) {
          $this->stream->write(' '.$key.'="'.htmlspecialchars($value).'"');
        }
        $this->stream->write('?>');
      } else {
        $this->stream->write(' '.$content.'?>');
      }
    }

    /**
     * Writes an entire element
     *
     * @param   string name
     * @param   string content
     * @param   [:string] attributes
     */
    public function writeElement($name, $content= NULL, $attributes= array()) {
      $this->stream->write('<'.$name);
      foreach ($attributes as $key => $value) {
        $this->stream->write(' '.$key.'="'.htmlspecialchars($value).'"');
      }
      
      if (NULL === $content) {
        $this->stream->write('/>');
      } else {
        $this->stream->write('>'.htmlspecialchars($content).'</'.$name.'>');
      }
    }
  }
?>
