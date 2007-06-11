<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'security.Policy',
    'security.PolicyException',
    'security.Permission'
  );
  
  // Parser states: General
  define('PF_ST_INITIAL',  0x0000);
  define('PF_ST_GRANT',    0x0001);
  define('PF_ST_BREAK',    0x0999);
  
  // Parser states: Errors
  define('PF_ST_EPARSE',   0x1000);
  define('PF_ST_EGRANT',   0x1001);
  define('PF_ST_EPERM',    0x1002);
  define('PF_ST_ESTATE',   0x1003);
  define('PF_ST_EREFLECT', 0x1004);
  
  // Parser states: Done
  define('PF_ST_DONE',     0xFFFF);
 
  /**
   * Policy
   *
   * @purpose  Categorizes grants
   * @see      http://java.sun.com/security/jaas/apidoc/javax/security/auth/Policy.html
   * @see      http://java.sun.com/j2se/1.4.1/docs/api/java/security/package-summary.html
   * @see      xp://security.Permission
   */
  class Policy extends Object {
    public
      $permissions  = array();
      
    /**
     * Add a permission
     *
     * @param   security.Permission p
     * @return  security.Permission the added permission
     */
    public function addPermission($p) {
      $this->permissions[]= $p;
      return $p;
    }
    
    /**
     * Create a policy from a file
     *
     * <code>
     *   uses('security.Policy', 'io.File');
     *
     *   try(); {
     *     $policy= &Policy::fromFile(new File('my.policy'));
     *   } if (catch('PolicyException', $e)) {
     *     $e->printStackTrace();
     *     exit();
     *   }
     *
     *   echo $policy->toString();
     * </code>
     *
     * @see     http://java.sun.com/j2se/1.4.1/docs/guide/security/PolicyFiles.html
     * @param   io.Stream stream
     * @return  security.Policy policy
     */
    public static function fromFile($stream) {
      static $errors= array(
        PF_ST_EPARSE    => 'Parse error', 
        PF_ST_EGRANT    => 'Grant syntax error',
        PF_ST_EPERM     => 'Permission syntax error',
        PF_ST_ESTATE    => 'State error',
        PF_ST_EREFLECT  => 'Reflection error'
      );
      
      $policy= new Policy();
      
      $stream->open(FILE_MODE_READ);
      $state= PF_ST_INITIAL;
      $num= 0;
      do {
        while (!$stream->eof() && FALSE !== ($line= $stream->readLine())) {
          $num++;
          
          // Ignore empty lines
          if (empty($line)) continue;
          
          switch ($state) {
            case PF_ST_INITIAL:
              switch ($line{0}) {
                case '/': 
                  if ('/' != $line{1}) {
                    $state= PF_ST_EPARSE;
                    $message= 'expecting "/", have "'.$line{1}.'"';
                    break 4;
                  }
                  $line= substr($line, 1);

                  // break missing intentionally
                case '#':
                case ';':
                  // TBD: Put comments somewhere?
                  break;

                case 'g':

                  // grant {           
                  // grant signedBy "Duke" {
                  // grant signedBy "sysadmin", codeBase "file:/home/sysadmin/" {
                  if ('rant' == substr($line, 1, 4)) {
                    $state= PF_ST_GRANT;
                    $end= FALSE;
                    $t= strtok(substr($line, 5), " \t,");
                    do {
                      switch ($t) {
                        case 'signedBy':
                          $signer= strtok('"');
                          break;
                          
                        case 'codeBase':
                          $codebase= strtok('"');
                          break;
                          
                        case '{': // End
                          $end= TRUE;
                          break 2;
                          
                        default:
                          $state= PF_ST_EGRANT;
                          $message= 'unknown grant token "'.$t.'"';
                          break 6;
                      }
                    } while ($t= strtok(" \t,"));
                    
                    // OK
                    if ($end) break;
                    
                    $state= PF_ST_EGRANT;
                    $message= 'expecting {';
                  }
                  break 4;
                  
                default:
                  $state= PF_ST_EPARSE;
                  $message= 'unexpected input "'.$line.'"';
                  break 4;
              }
              break;
              
            case PF_ST_GRANT:
              if (';' != $line{strlen($line)- 1}) {
                $state= PF_ST_EPARSE;
                $message= 'permission line not terminated by ";"';
                break 3;
              }
              
              if ('}' == $line{0}) {
                $state= PF_ST_INITIAL;
                break;
              }
              
              // permission java.util.PropertyPermission "java.vendor", "read";
              // permission java.security.SecurityPermission "Security.insertProvider.*";
              if (FALSE === ($p= strpos($line, 'permission'))) {
                $state= PF_ST_EPARSE;
                $message= 'expecting permission';
                break 3;
              }
              
              if (
                (FALSE === ($class= strtok(substr($line, $p+ 10), " \t;"))) ||
                (FALSE === ($name= strtok('"')))
              ) {
                $state= PF_ST_EPERM;
                $message= 'class and name required but not found';
                break 3;
              }
              
              try {
                $permission= XPClass::forName($class);
              } catch (ClassNotFoundException $e) {
                $state= PF_ST_EREFLECT;
                $message= $e->message;
                break 3;
              }
              
              // Parse actions
              $actions= array();
              while ($t= strtok(', ";')) $actions[]= $t;
              
              // Add permission
              $policy->addPermission($permission->newInstance($name, $actions));
              
              break;
          }
        }
        
        if (PF_ST_INITIAL != $state) {
          $state= PF_ST_ESTATE;
          $message= 'unterminated section';
        } else {
          $state= PF_ST_DONE;
        }
        
      } while (PF_ST_BREAK > $state);
      
      // Close stream
      $stream->close();
      
      if (PF_ST_DONE == $state) return $policy;
      
      // Errors
      throw(new PolicyException(sprintf(
        "%s in %s on line %d: %s",
        $errors[$state],
        $stream->uri,
        $num,
        $message
      )));
    }

  }
?>
