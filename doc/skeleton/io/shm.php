<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  require('lang.base.php');
  uses('io.sys.ShmSegment', 'util.cmd.ParamString');
  
  $p= &new ParamString();
  $s= &new ShmSegment('testsegment');
  
  try(); {
    switch ($p->value(1)) {
      case 'empty':
        var_dump($s->isEmpty());
        break;
      
      case 'get':
        var_dump($s->get());
        break;

      case 'put':
        list($type, $data)= explode(':', $p->value(2));
        var_dump($s->put(cast($data, $type)));
        break;
        
      case 'puto':
        $data= &new stdClass();
        $data->a= 'a';
        $data->b= 'b';
        var_dump($s->put($data));
        break;

      case 'puta':
        $data= array(
          'Hello',
          'World'
        );
        var_dump($s->put($data));
        break;

      case 'remove':
        var_dump($s->remove());
        break;

      default:
        printf(
          "Usage: %s <operation> [<options>]\n".
          "       <operation> is one of:\n".
          "       - empty  : Test whether this segment is empty\n".
          "       - get    : Get segment's contents\n".
          "       - put    : Put a scalar [options is type:value, e.g. put string:Test]\n".
          "       - puta   : Put an array\n".
          "       - puto   : Put an object\n".
          "       - remove : Remove\n",
          basename($p->value(0))
        );
    }
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
  
  exit();
?>
