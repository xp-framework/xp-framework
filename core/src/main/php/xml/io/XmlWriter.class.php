<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xml.io';

  /**
   * Outputs XML.
   *
   */
  abstract class xml·io·XmlWriter extends Object {
  
    /**
     * Start writing a document
     *
     * @param   string version default "1.0"
     * @param   string encoding defaults to XP default encoding
     * @param   bool standalone default FALSE
     */
    public abstract function startDocument($version= '1.0', $encoding= xp::ENCODING, $standalone= FALSE);

    /**
     * Close document. Will close all opened tags.
     *
     */
    public abstract function closeDocument();
    
    /**
     * Open an element
     *
     * @param   string name
     * @param   [:string] attributes
     */
    public abstract function startElement($name, $attributes= array());
    
    /**
     * Close an element previously opened with startElement()
     *
     */
    public abstract function closeElement();

    /**
     * Start a comment
     *
     */
    public abstract function startComment();
    
    /**
     * Close a comment
     *
     */
    public abstract function closeComment();

    /**
     * Start a CDATA section
     *
     */
    public abstract function startCData();
    
    /**
     * Close a CDATA section
     *
     */
    public abstract function closeCData();

    /**
     * Start a processing instruction.
     *
     * @param   string target
     */
    public abstract function startPI($target);
    
    /**
     * Close a processing instruction
     *
     */
    public abstract function closePI();

    /**
     * Writes text
     *
     * @param   string content
     */
    public abstract function writeText($content);

    /**
     * Writes an entire CDATA section
     *
     * @see     http://www.w3.org/TR/xml/#sec-cdata-sect
     * @param   string content
     */
    public abstract function writeCData($content);

    /**
     * Write raw string. No escaping or checks are performed on the content,
     * XML conformity of the output document is not guaranteed - it depends
     * on the given input.
     *
     * @param   string content
     */
    public abstract function writeRaw($content);

    /**
     * Writes a comment
     *
     * @see     http://www.w3.org/TR/xml/#sec-comments
     * @param   string content
     */
    public abstract function writeComment($content);

    /**
     * Writes a processing instruction
     *
     * @see     http://www.w3.org/TR/xml/#sec-pi
     * @param   string target
     * @param   var content either a string or a map with attributes
     */
    public abstract function writePI($target, $content);

    /**
     * Writes an entire element
     *
     * @param   string name
     * @param   string content
     * @param   [:string] attributes
     */
    public abstract function writeElement($name, $content= NULL, $attributes= array());
  }
?>
