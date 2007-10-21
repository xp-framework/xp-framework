<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: master.xsl 4689 2005-02-20 00:30:14Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="exsl func php"
>

  <func:function name="func:linkPage">
    <xsl:param name="no"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="$no = 0">/</xsl:when>
        <xsl:otherwise><xsl:value-of select="concat('/page/', $no)"/></xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:linkAlbum">
    <xsl:param name="name"/>
    
    <func:result>
      <xsl:value-of select="concat('/album/', $name)"/>
    </func:result>
  </func:function>
  
  <func:function name="func:linkCollection">
    <xsl:param name="name"/>
    
    <func:result>
      <xsl:value-of select="concat('/collection/', $name)"/>
    </func:result>
  </func:function>
  
  <func:function name="func:linkChapter">
    <xsl:param name="album"/>
    <xsl:param name="no"/>
    
    <func:result>
      <xsl:value-of select="concat('/album/', $album, '/', $no)"/>
    </func:result>
  </func:function>
  
  <func:function name="func:linkImage">
    <xsl:param name="album"/>
    <xsl:param name="chapter"/>
    <xsl:param name="type"/>
    <xsl:param name="id"/>
    
    <func:result>
      <xsl:value-of select="concat('/album/', $album, '/', $chapter, '/', $type, ',', $id)"/>
    </func:result>
  </func:function>

  <func:function name="func:linkShot">
    <xsl:param name="shot"/>
    <xsl:param name="no"/>
    
    <func:result>
      <xsl:value-of select="concat('/shot/', $shot, '/', $no)"/>
    </func:result>
  </func:function>
</xsl:stylesheet>
