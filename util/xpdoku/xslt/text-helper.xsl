<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>

  <xsl:template match="advanced">
    <xsl:call-template name="frame">
      <xsl:with-param name="margin" select="'4'"/>
      <xsl:with-param name="icolor" select="'#eaffea'"/>
      <xsl:with-param name="color" select="'#6a996a'"/>
      <xsl:with-param name="content">
        <span style="line-height: 18px">
          <b>Advanced topics:</b><br/>
          <xsl:for-each select="ref">
            <img src="/image/caret-r.gif" height="7" width="11" alt="{position()}" hspace="2" vspace="0"/> <xsl:apply-templates select="."/><br/>
          </xsl:for-each>
        </span>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>
  
  <xsl:template match="box">
    <br/><br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="margin" select="'4'"/>
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="icolor" select="'#f6f6f6'"/>
      <xsl:with-param name="content">
        <xsl:if test="@caption">
          <b><xsl:value-of select="@caption"/></b>
          <br/>
        </xsl:if>
        <xsl:apply-templates/>
      </xsl:with-param>
    </xsl:call-template>
    <br/>
  </xsl:template>
  
  <xsl:template match="code">
    <br/><br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        <code>
          <xsl:apply-templates select="document(concat('php://', .))"/>
        </code>
      </xsl:with-param>
    </xsl:call-template>
    <br/>
  </xsl:template>

  <xsl:template match="image">
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        <center>
          <b style="line-height: 20px"><xsl:value-of select="@alt"/></b><br/>
          <img hspace="4" vspace="4" border="0">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="src">/image/content/<xsl:value-of select="@src"/></xsl:attribute>
          </img>
        </center>
      </xsl:with-param>
    </xsl:call-template>
    <br/>
  </xsl:template>

  <xsl:template match="text//br|text//tt|php//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="ref[@type= 'ext']">
    <a title="External link to {@link}" href="{@link}" target="_new"><xsl:value-of select="."/></a>
  </xsl:template>

  <xsl:template match="ref[@type= 'google']">
    <a href="http://google.de/search?q={@link}" target="_new">Google search: <xsl:value-of select="@link"/></a>
  </xsl:template>

  <xsl:template match="ref[@type= 'api:class']">
    <xsl:param name="label" select="."/>
    <xsl:param name="link">
      <xsl:choose>
        <xsl:when test="contains(@link, '#')">
          <xsl:value-of select="concat(substring-before(@link, '#'), '.html#', substring-after(@link, '#'))"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="concat(@link, '.html')"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <a title="XP API-Doc: Class {@link}" href="/apidoc/classes/{$link}">
      <xsl:value-of select="$label"/>
      <xsl:if test="$label = ''"><xsl:value-of select="@link"/></xsl:if>
    </a>
    <img alt="Link" src="/image/ext.gif" height="11" width="11" border="0" vspace="0" hspace="2"/>
  </xsl:template>

  <xsl:template match="ref[@type= 'api:collection']">
    <xsl:param name="label" select="."/>
    <a title="XP API-Doc: Collection {@link}" href="/apidoc/collections/{@link}.html">
      <xsl:value-of select="$label"/>
      <xsl:if test="$label = ''"><xsl:value-of select="@link"/></xsl:if>
    </a>
    <img alt="Link" src="/image/ext.gif" height="11" width="11" border="0" vspace="0" hspace="2"/>
  </xsl:template>
  
  <xsl:template match="ref">
    <a href="/content/{@link}.html"><xsl:value-of select="."/></a>
  </xsl:template>
  
</xsl:stylesheet>
