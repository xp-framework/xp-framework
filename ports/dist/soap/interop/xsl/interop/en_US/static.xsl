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
    <!-- TDB: Add links / news -->
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>SOAP interop test suite</h1>
    
    <!-- Intro to SOAP interop -->
    <h3>What is SOAP?</h3>
    <p>
      SOAP (formerly an acronym of Simple Object Access Protocol) is a light-weight protocol 
      for exchanging messages between computer software, typically in the form of software 
      components. The word object implies that the use should adhere the object-oriented 
      programming programming paradigm.<br/>
      SOAP is an extensible and decentralized framework that can work over multiple computer 
      network protocol stacks. Remote procedure calls can be modeled as an interaction of 
      several SOAP messages. SOAP is one of the enabling protocols for Web services.<br/>
      This site is dedicated to SOAPs use over the HTTP protocol - used by every web browser.
    </p>
    
    <h3>What is this site for?</h3>
    <p>
      This site's purpose is to improve the quality of the XP Frameworks SOAP server and client
      implementation.<br/>
      On these pages, you can find information about how to include the XP frameworks SOAP server
      into your SOAP interop tests. Furthermore, you can see the test results of the XP
      Frameworks SOAP client implementation.
    </p>
    
    <h3>I have found a bug in your implementation</h3>
    <p>
      Ok, thanks for that. Please submit your bug using the projects 
      <a href="http://bugs.xp-framework.net/" target="_blank">Bugzilla</a>.<br/>
      If you feel uncomfortable with that, you can drop us a mail describing the
      bug at <a href="mailto:xp@php3.de">xp@php3.de</a>.
    </p>
  </xsl:template>
</xsl:stylesheet>
