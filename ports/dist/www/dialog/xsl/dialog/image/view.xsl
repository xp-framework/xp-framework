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
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h3>
      <a href="{func:link('static')}">Home</a> &#xbb; 
      <a href="{func:link(concat('album/view?', /formresult/album/@name))}">
        <xsl:value-of select="/formresult/album/@title"/>
      </a> &#xbb; 
      <xsl:value-of select="/formresult/selected/name"/>
    </h3>

    <br clear="all"/> 
    <center>
      <a title="Previous image" class="pager" id="{/formresult/selected/@id &gt; 0}">
        <xsl:if test="/formresult/selected/@id &gt; 0">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'image/view?', 
            /formresult/album/@name, ',',
            /formresult/selected/@type, ',',
            /formresult/selected/@chapter, ',',
            /formresult/selected/@id - 1
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Next image" class="pager" id="{/formresult/selected/@last = ''}">
        <xsl:if test="/formresult/selected/@last = ''">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'image/view?', 
            /formresult/album/@name, ',',
            /formresult/selected/@type, ',',
            /formresult/selected/@chapter, ',',
            /formresult/selected/@id + 1
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>
    
    <table width="100%" border="0">
      <tr>
        <td id="image" align="center">
          <img src="/albums/{/formresult/album/@name}/{/formresult/selected/name}"/>
        </td>
      </tr>
    </table>
    
    <!--
    <xmp>
      <xsl:copy-of select="/formresult/selected"/>
    </xmp>
    -->
  </xsl:template>
  
</xsl:stylesheet>
