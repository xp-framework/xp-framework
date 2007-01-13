<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="wizard.inc.xsl"/>
  
  <xsl:template match="/">
    <html>
      <head>
        <title><xsl:value-of select="$__state"/> Editor</title>
        <link rel="stylesheet" type="text/css" href="/style.css"/>
      </head>
      <body>
        <div id="main">
          <h1>
            <xsl:value-of select="$__state"/>
          </h1>
          <xsl:call-template name="realize-view">
            <xsl:with-param name="handler" select="/formresult/handlers/handler[1]"/>
          </xsl:call-template>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
