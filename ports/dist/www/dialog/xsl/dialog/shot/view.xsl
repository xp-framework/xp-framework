<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for shots/view
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
      Featured image: <xsl:value-of select="/formresult/selected/name"/>
    </h3>

    <br clear="all"/> 
    <center>
      <a title="Color version" class="pager{/formresult/selected/@mode = 'gray'}" id="previous">
        <xsl:if test="/formresult/selected/@mode = 'gray'">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'shot/view?', 
            /formresult/selected/name, 
            ',0'
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Black and white version" class="pager{/formresult/selected/@mode = 'color'}" id="next">
        <xsl:if test="/formresult/selected/@mode = 'color'">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'shot/view?', 
            /formresult/selected/name, 
            ',1'
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>
    
    <!-- Selected image -->
    <table width="100%" border="0">
      <tr>
        <td id="image" align="center">
          <img border="0" src="/shots/{/formresult/selected/@mode}.{/formresult/selected/fileName}"/>
        </td>
      </tr>
    </table>
    
    <p>
      Originally taken on <xsl:value-of select="func:datetime(/formresult/selected/image/exifData/dateTime)"/>
      with <xsl:value-of select="/formresult/selected/image/exifData/make"/>'s
      <xsl:value-of select="/formresult/selected/image/exifData/model"/>.
    </p>
    
    <!--
    <xmp>
      <xsl:copy-of select="/formresult/selected"/>
    </xmp>
    -->
  </xsl:template>
  
</xsl:stylesheet>
