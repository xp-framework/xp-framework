<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for resources page
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

    <!-- see also -->
    <h4 class="context">See also</h4>
    <ul class="context">
      <li>
        <em>cvsweb</em>:<br/>
        <a href="http://cvs.xp-framework.net/" target="_cvs">Browse CVS repository<img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
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
    <h1>resources</h1>

    <h3>
      current releases
    </h3>

    <p>
      <a href="#download?xp-2003-12-18.tar.gz"><b>xp-2003-12-18.tar.gz</b><img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
       - 921.50 KB [ MD5: 1675122f00cc6f321c83176f8e06bd05 ]
    </p>
    <ul>
      <li>DomXSL support</li>
      <li>with() syntactic sugar</li>
      <li>Telephony API fixup</li>
      <li>QA: CS/WS fixes</li>
      <li>API-Doc parser: Recognize @model</li>
      <li>Scriptlet API improvements</li>
      <li>Session fixes</li>
      <li>Bugzilla db and mailgateway packages</li>
      <li>Nagios db package</li>
      <li>Encoding fixes in peer.Mail</li>
      <li>DBConnection::prepare() fixes for large ints</li>
      <li>MySQL timestamp support</li>
      <li>ClassProfiler fixes</li>
      <li>Node::getSource() fixes for large ints</li>
      <li>SOAP type mapping support</li>
      <li>SOAP Hashmap fix for non-string keys</li>
      <li>New com.sun.webstart package</li>
      <li>New registry storages DBA and Flatfile</li>
    </ul>

    <p>
      <a href="#download?xp-2003-10-26.tar.gz"><b>xp-2003-10-26.tar.gz</b><img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
       - 912.05 KB [ MD5: 2715aa9e01e5f58f1a0990e2b52d50d3 ]
    </p>
    <ul>
      <li>peer.mail fixes / API simplification</li>
      <li>Quality assurance work</li>
    </ul>

    <p>
      <a href="#download?xp-2003-10-18.tar.gz"><b>xp-2003-10-18.tar.gz</b><img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
       - 910.44 KB [ MD5: e4cf0a4735f2164a3ac79b2e41b19938 ]
    </p>
    <ul>
      <li>CGI compatibility via xp::sapi('cgi')</li>
      <li>delete() core functionality</li>
      <li>Iterator interface</li>
      <li>IRC API extension: onPings()</li>
      <li>Numerous cosmetic changes [QA]</li>
      <li>Date class fix for invalid dates</li>
      <li>SOAP multiref support and Axis compat fixes</li>
      <li>DBM support via collection io.dba </li>
    </ul>

    <!-- anoncvs -->
    <h3>anonymous cvs</h3>
    <p>
      We now offer an anonymous cvs access. Check it out with:
      <pre>cvs -d:pserver:anonymous@php3.de:/home/cvs/repositories/xp co .</pre>
      (Password is empty).
    </p>
  </xsl:template>
  
</xsl:stylesheet>
