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
    
    <h3>
      Test defails for <xsl:value-of select="/formresult/detail/@type"/>
      in method "<xsl:value-of select="/formresult/detail/@method"/>"
      with service <xsl:value-of select="/formresult/detail/@service"/>
    </h3>
    
    <p>
      <pre>
        <xsl:value-of select="/formresult/detail"/>
      </pre>
    </p>
  </xsl:template>
</xsl:stylesheet>
