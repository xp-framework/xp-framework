<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="../../../common/xsl/layout.inc.xsl"/>
  
  <xsl:variable name="navigation">
    <area name="dev"/>
  </xsl:variable>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>

  <xsl:template name="tracking-code">UA-617805-6</xsl:template>

</xsl:stylesheet>
