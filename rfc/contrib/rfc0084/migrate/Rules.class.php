<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'DeprecatedRule',
    'RenamedRule',
    'MovedRule'
  );

  /**
   * Rule definitions
   *
   * @purpose  Rule container
   */
  class Rules extends Object {
  
    /**
     * Returns all rules
     *
     * @model   static
     * @access  public
     * @param   array<string, &Rule> rules
     */
    function allRules() {
      return array(
        'gui.gtk'                 => new MovedRule('org.gnome'),
        'org.json'                => new RenamedRule('webservices.json'),
        'xml.xmlrpc'              => new RenamedRule('webservices.xmlrpc'),
        'xml.soap'                => new RenamedRule('webservices.soap'),
        'xml.wddx'                => new RenamedRule('webservices.wddx'),
        'xml.uddi'                => new RenamedRule('webservices.uddi'),
        'xml.xp'                  => new DeprecatedRule(array('xml.meta')),
        'io.cca'                  => new DeprecatedRule(array('lang.archive')),
        'util.profiling.unittest' => new RenamedRule('unittest'),
        'util.archive'            => new MovedRule('org.gnu.tar'),
        'util.adt'                => new DeprecatedRule(array('util.collections')),
        'util.registry'           => new DeprecatedRule(),
        'util.mp3'                => new MovedRule('de.fraunhofer.mp3'),
        'peer.ajp'                => new MovedRule('org.apache.ajp'),
        'peer.cvsclient'          => new MovedRule('org.cvshome'),
        'text.apidoc'             => new DeprecatedRule(array('text.doclet')),
        'text.translator'         => new MovedRule('net.schweikhardt'),
        'net.planet-xp'           => new DeprecatedRule(),
        'ch.ecma'                 => new MovedRule('ch.ecma'),
        'com.capeconnect'         => new MovedRule('com.capeconnect'),
        'com.flickr'              => new MovedRule('com.flickr'),
        'com.google'              => new MovedRule('com.google'),
        'com.microsoft'           => new MovedRule('com.microsoft'),
        'com.simpy'               => new MovedRule('com.simpy'),
        'com.sun'                 => new MovedRule('com.sun'),
        'com.xmlrpc'              => new MovedRule('com.xmlrpc'),
        'net.xmethods'            => new MovedRule('net.xmethods'),
        'net.xp_framework'        => new MovedRule('net.xp_framework'),
        'org.bugzilla'            => new MovedRule('org.bugzilla'),
        'org.cvshome'             => new MovedRule('org.cvshome'),
        'org.dia'                 => new MovedRule('org.dia'),
        'org.dict'                => new MovedRule('org.dict'),
        'org.fpdf'                => new MovedRule('org.fpdf'),
        'org.gnu'                 => new MovedRule('org.gnu'),
        'org.htdig'               => new MovedRule('org.htdig'),
        'org.ietf'                => new MovedRule('org.ietf'),
        'org.imc'                 => new MovedRule('org.imc'),
        'org.isbn'                => new MovedRule('org.isbn'),
        'org.nagios'              => new MovedRule('org.nagios'),
        'org.tigris'              => new MovedRule('org.tigris'),
        'org.webdav'              => new MovedRule('org.webdav'),
        'us.icio'                 => new MovedRule('us.icio'),
      );
    }
  }
?>
