<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
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
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="../../../common/xsl/layout.inc.xsl"/>
  <xsl:variable name="navigation">
    <area name="news"/>
  </xsl:variable>

  <xsl:template match="pager">
    <div style="text-align: center;">
      <a href="{xp:link(concat($__state, '?', /formresult/current-category/@id, ',', @prev))}">&lt;&lt;&lt;</a>
      |
      <a href="{xp:link(concat($__state, '?', /formresult/current-category/@id, ',', @next))}">&gt;&gt;&gt;</a>
    </div>
  </xsl:template>
</xsl:stylesheet>