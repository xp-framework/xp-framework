<?php
/* This class is part of the XP framework
 *
 * $Id: UskaContext.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska::scriptlet;

  ::uses(
    'scriptlet.xml.workflow.Context',
    'de.uska.db.Player'
  );

  /**
   * Provide context information for uska.
   *
   * @purpose  Uska context
   */
  class UskaContext extends scriptlet::xml::workflow::Context {
    public
      $user=          NULL,
      $permissions=   NULL,
      $eventtypes=    array();
    
    /**
     * Set up the context.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     */
    public function setup($request) {
      $cm= rdbms::ConnectionManager::getInstance();
      $db= $cm->getByHost('uska', 0);
      
      $this->eventtypes= array();
      try {
        $q= $db->query('select event_type_id, name, description from uska.event_type');
        while ($q && $r= $q->next()) { $this->eventtypes[$r['event_type_id']]= array(
          'type'  => $r['name'],
          'name'  => $r['description']
          );
        }
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
    }
    
    /**
     * Process the context.
     *
     * @param   &scriptlet.HttpScriptletRequest request
     * @throws  lang.IllegalAccessException to indicate an error
     */
    public function process($request) {
      if ($this->user) {
        $cookie= $request->getCookie('uska-user');
        if (!is('scriptlet.Cookie', $cookie) || !$this->user->getUsername() == $cookie->getValue()) {
          $log= util::log::Logger::getInstance();
          $cat= $log->getCategory();
          
          $cat->warn('User', $this->user->getUsername(), 'has exposed his session id to user', $cookie);
          $cat->warn('Destroying session', $request->getSessionId());
          
          // Build URL we have to forward to...
          $uri= $request->getUri();
          $pathinfo= sscanf($uri['path'], '/xml/%[^.].%[^./].psessionid=%[^/]/%s');
          
          $this->_forwardTo= sprintf('%s://%s/xml/%s.%s/%s%s',
            $uri['scheme'],
            $uri['host'],
            $request->getProduct(),
            $request->getLanguage(),
            $pathinfo[3],
            strlen($request->getQueryString()) ? '?'.$request->getQueryString() : ''
          );
          
          $cat->debug($uri, $pathinfo, $this->_forwardTo);
        }
      }
    }

    /**
     * Insert status information to result tree
     *
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function insertStatus($response) {
      if (isset($this->_forwardTo)) {

        // Forward to same page without session (session hijacking)
        $response->sendRedirect($this->_forwardTo);
        return;
      }
      
      if ($this->user) {
        $n= $response->addFormResult(::fromObject($this->user, 'user'));
        $n->addChild(::fromArray(array_keys($this->permissions), 'permissions'));
      }
      
      $enode= $response->addFormResult(new ('eventtypes'));
      foreach ($this->eventtypes as $id => $desc) {
        $enode->addChild(new ('type', $desc['name'], array(
          'id' => $id,
          'type' => $desc['type']
        )));
      }
    }
    
    /**
     * Set user.
     *
     * @param   &de.uska.db.Player user
     */
    public function setUser($user) {
      $this->user= $user;
      $this->setChanged();
    }
    
    /**
     * Set permissions
     *
     * @param   &array perms
     */
    public function setPermissions($perm) {
      $this->permissions= $perm;
      $this->setChanged();
    }
    
    /**
     * Check whether user has a certain permission
     *
     * @param   string name
     * @return  bool
     */
    public function hasPermission($name) {
      return isset($this->permissions[$name]);
    }
  }
?>
