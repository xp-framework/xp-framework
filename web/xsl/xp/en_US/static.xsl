<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- news -->
    <h4 class="context">Newsflash</h4>
    <ul class="context">
      <li>
        <em>2003-10-26 18:31</em>:<br/>
        <a href="#news?__id=3773277">XP2 Alpha</a>
      </li>
      <li>
        <em>2003-09-28 18:33</em>:<br/>
        <a href="#news?__id=3773276">Examples</a>
      </li>
      <li>
        <em>2003-09-27 15:30</em>:<br/>
        <a href="#news?__id=3773275">[rdbms] Exception refactoring</a>
      </li>
      <li>
        <em>2003-09-21 20:29</em>:<br/>
        <a href="#news?__id=3773274">Core feature: Interfaces</a>
      </li>
    </ul>

    <!-- cvs -->
    <h4 class="context">CVS activity</h4>
    <ul class="context">
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">StliConnection</a> (friebe)
      </li>
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">TelephonyAddress</a> (friebe)
      </li>
      <li>
        <em>2003-09-27 15:30:00</em>:<br/>
        <a href="#apidoc/classes/com/sun/webstart/JnlpDocument">JnlpDocument</a> (friebe)
      </li>
    </ul>

    <!-- release -->
    <h4 class="context">Current release</h4>
    <ul class="context">
      <li>
        <em>2003-10-26</em>:<br/>
        <a href="#release/2003-10-26">Download</a> | <a href="#changelog/2003-10-26">Changelog</a>
      </li>
    </ul>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>use::xp (r1.2)</h1>

    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="intro">
      <tr>
        <td>
          <img src="/image/create.gif" width="295" height="100"/>
        </td>
        <td>
          <ul class="intro">
            <li>Command-line tools and cronjobs</li>
            <li>GUI applications with the power of GTK-PHP</li>
            <li>Web sites using HTML or XML/XSL</li>
            <li>SOAP clients/servers</li>
            <li>Cron jobs</li>
            <li>Daemons (TCP/IP client/server architecture)</li>
          </ul>
        </td>
      </tr>
    </table>

    <h3>
      What is xp?
    </h3>
    <p>
      XP is a modular framework for <a href="#deref?http://php.net/" target="_new">PHP<img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
      consisting of a number of useful classes, making it easier to complete everyday tasks.
      <a href="#content/tasks">Learn more...</a>
    </p>

    <h3>
      I want to use the xp framework. What do I have to do?
    </h3>
    <p>
      Grab yourself any of the releases at <a href="http://xp-framework.net/resources">http://xp-framework.net/resources</a>
      or check out a copy of CVS head using anonymous CVS (instructions at the same URL).
      Follow the <a href="#/content/about.install.html">installation instruction</a>, have
      a look at the <a href="#/content/examples.html">examples section</a> and you're ready to go!
    </p>

    <h3>
      News: XP2 Alpha (2003-10-26 18:31)
    </h3>
    <p>
      An initial release of the Zend Engine 2 port of the XP framework
      is available for download. Note that the classes contained therein where
      automatically migrated and - though syntax-checked - may still contain
      logical errors or other incompatibilities. It should be good enough to
      start playing around with, though, so have a look, experiment and report
      any bugs you can find. Also note that some classes will change due to
      PHP5's capabilities - do not assume this is a stable API for now.
    </p>

    <h3>
      Further reading tip: Extreme Programming
    </h3>
    <p>
      Extreme Programming (or XP) is a set of values, principles and practices
      for rapidly developing high-quality software that provides the highest
      value for the customer in the fastest way possible. XP is extreme in the
      sense that it takes 12 well-known software development "best practices"
      to their logical extremes -- turning them all up to "10" (or "11" for
      Spinal Tap fans). See <a href="#deref?http://www.jera.com/techinfo/xpfaq.html">Kent Beck's introduction to Extreme Programming Explained<img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
      for more details.
    </p>
  </xsl:template>
  
</xsl:stylesheet>
