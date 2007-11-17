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
  <func:function name="xp:linkCategory">
    <xsl:param name="id"/>
    <xsl:param name="name"/>
    
    <func:result select="concat('/category/', $id, '/', $name, '/')"/>
  </func:function>
  
  <func:function name="xp:linkArticle">
    <xsl:param name="id"/>
    <xsl:param name="name"/>
    <xsl:param name="date"/>
    
    <func:result select="concat('/article/', $id, '/', xp:dateformat($date, 'Y/m/d/'), $name)"/>
  </func:function>
</xsl:stylesheet>