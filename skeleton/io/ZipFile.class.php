<?php
/*
 * $Id$
 *
 * Diese Klasse ist Bestandteil des XP-Frameworks
 * (c) 2001 Timm Friebe, Schlund+Partner AG
 *
 * @see http://doku.elite.schlund.de/projekte/xp/skeleton/
 *
 */
 
  uses('io.File');

  class ZipFile extends File {
  
    /**
     * Datei öffnen
     *
     * @param   string mode Öffnen-Modus
     * @param   string compression Kompressions-Level
     * @throws  IllegalStateException, wenn keine URI angegeben ist
     * @throws  FileNotFoundException, wenn Datei nicht gefunden wurde
     */
    function open($mode= FILE_MODE_READ, $compression) {
      if (empty($this->uri)) return throw(new IllegalStateException('no uri defined'));
      $this->mode= $mode;
      if (
        ('php://' != substr($this->uri, 0, 6)) &&
        ($mode== FILE_MODE_READ) && 
        (!$this->exists())
      ) return throw(new FileNotFoundException($this->uri));
      
      // Öffnen
      $this->_fd= gzopen($this->uri, $this->mode.$compression);
      if (!$this->_fd) return throw(new IOException('cannot open '.$this->uri.' mode '.$this->mode));
      return TRUE;
    }
    
    /**
     * Zeile auslesen. \r oder \n werden abgeschnitten
     *
     * @param   (int)bytes default 4096 Anzahl max. zu lesender Bytes
     * @return  (string)line Gelesene Bytes
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function readLine($bytes= 4096) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= chop(gzgets($this->_fd, $bytes));
      if (is_error() && $result === FALSE) {
        return throw(new IOException('readLine() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Zeichen auslesen
     *
     * @return  (char)result Gelesenes Zeichen
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function readChar() {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzgetc($this->_fd);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('readChar() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Lesen (max $bytes Zeichen oder bis zum Zeilenende)
     *
     * @param   (int)bytes default 4096 Anzahl max. zu lesender Bytes
     * @return  (string)line Gelesene Bytes
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function gets($bytes= 4096) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzgets($this->_fd, $bytes);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('gets() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Lesen
     *
     * @param   (int)bytes default 4096 Anzahl max. zu lesender Bytes
     * @return  (string)line Gelesene Bytes
     * @throws  IOException, wenn nicht gelesen werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function read($bytes= 4096) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzread($this->_fd, $bytes);
      if (is_error() && $result === FALSE) {
        return throw(new IOException('read() cannot read '.$bytes.' bytes from '.$this->uri));
      }
      return $result;
    }

    /**
     * Schreiben
     *
     * @return  (bool)success
     * @throws  IOException, wenn nicht geschrieben werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function write($string) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzwrite($this->_fd, $string);
      if (is_error() && $result === FALSE) {
        throw(new IOException('cannot write '.strlen($string).' bytes to '.$this->uri));
      }
      return $result;
    }

    /**
     * Schreiben. \n wird ergänzt
     *
     * @return  (bool)success
     * @throws  IOException, wenn nicht geschrieben werden kann
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function writeLine($string) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzputs($this->_fd, $string."\n");
      if (is_error() && $result === FALSE) {
        throw(new IOException('cannot write '.(strlen($string)+ 1).' bytes to '.$this->uri));
      }
      return $result;
    }
    
    /**
     * Check auf <<EOF>>
     *
     * @return  (bool)isEof
     * @throws  IllegalStateException, wenn keine Datei offen
     */
    function eof() {
      if (!isset($this->_fd)) {
        return throw(new IllegalStateException('file not open'));
      }
      return gzeof($this->_fd);
    }
    
    /**
     * Filepointer bewegen
     *
     * @param  (int)position default 0 Die Position
     * @param  (int)mode default SEEK_SET @see http://php.net/fseek
     * @throws  IOException, wenn der Pointer nicht an die Position gesetzt werden konnte
     * @throws  IllegalStateException, wenn keine Datei offen
     * @return (bool)success
     */
    function seek($position= 0, $mode= SEEK_SET) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzseek($this->_fd, $position);
      if (is_error() && FALSE === $result) return throw(new IOException('seek error, position '.$position.' in mode '.$mode));
      return TRUE;
    }
    
    /**
     * Filepointerposition ermitteln
     *
     * @throws  IllegalStateException, wenn keine Datei offen
     * @throws  IOException, wenn die Position nicht ermittelt werden kann
     * @return  (int)position
     */
    function tell($position= 0, $mode= SEEK_SET) {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gztell($this->_fd);
      if (is_error() && FALSE === $result) return throw(new IOException('cannot retreive file pointer\'s position'));
      return $result;
    }

    /**
     * Datei schließen
     *
     * @throws  IllegalStateException, wenn keine Datei offen
     * @return  (bool)success
     */
    function close() {
      if (!isset($this->_fd)) return throw(new IllegalStateException('file not open'));
      $result= gzclose($this->_fd);
      unset($this->_fd);
      return $result;
    }

  }
?>
