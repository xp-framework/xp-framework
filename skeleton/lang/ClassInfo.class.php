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

  define('COMMENT_ID_CLASS',    '__CLASS__');

  /**
   * Gibt Informationen zu einer Klasse zurück
   *
   */
  class ClassInfo extends Object {
    var 
      $className,
      $classPath;
      
    var
      $_comments;
    
    function __construct($params= NULL) {
      Object::__construct($params);
      if (isset($params['class'])) $this->setClass($params['class']);
    }
    
    function setClassFile($filename) {
      return $this->setClass(strtr($filename, array(
        '/'             => '.',
        '.class.php'    => '',
        SKELETON_PATH   => ''
      )));
    }
    
    function setClass($class) {
      try(); {
        import($class);
      } if ($e= catch(E_ANY_EXCEPTION)) {
        return throw($e);
      }      
      $parts= explode('.', $class);
      $this->className= $parts[sizeof($parts)- 1];
      $this->classPath= implode('.', array_slice($parts, 0, -1));
      if (!class_exists(strtolower($this->className))) {
        return throw(E_IO_EXCEPTION, 'noclassdeffounderror');
      }
    }
    
    function _new() {
      $reflect= strtolower($this->className);
      $this->object= &new $reflect;
    }
    
    function _destroy() {
      $this->object->__destruct();
    }
    
    function _parse() {
      $fd= fopen(SKELETON_PATH.'/'.strtr($this->classPath.'.'.$this->className, array(
        '.'     => '/'
      )).'.class.php', 'r');
      if (!$fd) {
        return throw(E_IO_EXCEPTION, $this->classPath.'.'.$this->className);
      }
      
      $this->_comments= array();
      $cid= -1;
      while (!feof($fd)) {
        $line= chop(fgets($fd, 4096));
        
        // CVS-Versions-Info suchen
        // Beispiel: $Id$
        if (preg_match(
          '#\$Id: [a-zA-Z]+\.class\.php,v ([0-9\.]+) ([0-9/\.]+ [0-9/:]+) ([a-z]+) Exp#', 
          $line, 
          $regs
        )) {
          $this->_version= new StdClass();
          $this->_version->num= $regs[1];
          $this->_version->date= $regs[2];
          $this->_version->author= $regs[3];
        }

        // Kommentar-Anfang suchen
        // DOC_Kommentare fangen mit /** an
        if (preg_match('=^[\s\t]*/\*\*[\s\t]*$=', $line)) {
          $comment= '';
          $cid= sizeof($this->_comments);
          continue;
        }

        // Kommentar-Ende?
        // DOC_Kommentare hören mit */ auf
        if (preg_match('=^[\s\t]*\*/[\s\t]*$=', $line) && $cid>= 0) {

          $c= new StdClass();
          $c->access= 'public';
          $c->return= new StdClass();
          $c->return->type= 'void';
          $c->params= array();
          $c->see= array();
          
          // In der nächsten Zeile kommt die Funktion. Sonst ist es Klassendoku
          // "    function unLock() {"
          $line= chop(fgets($fd, 4096));
          $cid= COMMENT_ID_CLASS;
          if (preg_match(
            '=^[\s\t]+function[\s\t]+&?([a-zA-Z_]+)[\s\t]*\([^\)]*\)[\s\t\{]*$=',
            $line,
            $regs
          )) {
            $c->function= $regs[1];
            $cid= strtolower($c->function);
          }
          $lines= explode("\n", $comment);
          foreach ($lines as $i=> $line) {
            if ('@' == substr($line, 0, 1)) {
              list($indicator, $doc)= preg_split('/\s+/', substr($line, 1), 2);
              switch ($indicator) {
                case 'return':
                  list(
                    $c->return->type, 
                    $c->return->comment
                  )= explode(' ', $doc, 2);
                  break;
                case 'param':
                  $param= new StdClass();
                  list(
                    $param->type,
                    $param->var,
                    $param->comment
                  )= explode(' ', $doc, 3);
                  if (preg_match('/^default ([^\s]+)/', $param->comment, $regs)) {
                    $param->default= $regs[1];
                    $param->comment= str_replace($regs[0], '', $param->comment);
                  }
                  $c->params[]= $param;
                  break;
                case 'see':
                  $c->see[]= $doc;
                  break;
                default:
                  $c->$indicator= $doc;
                  break;
              }
              unset($lines[$i]);
            }
          }
          $c->comment= chop(implode("\n", $lines));

          $this->_comments[$cid]= $c;
          $cid= -1;
        }

        if ($cid>= 0) $comment.= preg_replace('=^[^\*]+\* ?=', '', $line)."\n";
      }
      fclose($fd);
    }

    function getVersion() {
      if (!isset($this->_version)) $this->_parse();
      return $this->_version;
    }
    
    function getComments() {
      if (!isset($this->_comments)) $this->_parse();
      return $this->_comments;
    }
    
    function getComment($method) {
      if (!isset($this->_comments)) $this->_parse();
      if (!isset($this->_comments[$method])) return FALSE;
      return $this->_comments[$method];
    }
    
    function getMethods() {
      if (!isset($this->object)) $this->_new();
      return get_class_methods($this->object);
    }    
    
    function getMembers() {
      if (!isset($this->object)) $this->_new();
      return get_object_vars($this->object);
    }
    
    function getParent() {
      if (!isset($this->object)) $this->_new();
      return get_parent_class($this->object);
    }
  }
