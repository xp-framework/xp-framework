<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overrideables
 !
 ! $Id: master.xsl 4410 2004-12-18 18:19:28Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 remote-result-prefixes="func php exsl xsl"
>
  <xsl:template name="html-head"/>
  <xsl:template name="html-title">XP Framework</xsl:template>
</xsl:stylesheet>