<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Shows a package's apidoc
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:str="http://exslt.org/strings"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func str"
 exclude-result-prefixes="func php exsl xsl xp str"
>
  <xsl:include href="doc.inc.xsl"/>

  <xsl:template match="package">
    <xsl:variable name="package" select="concat(@name, '.')"/>
    <div id="breadcrumb">
      <a href="{xp:link('api')}">API documentation</a> &#xbb;
      <xsl:call-template name="hierarchy">
        <xsl:with-param name="path" select="@name"/>
      </xsl:call-template>
    </div>
    <h1>
      package <xsl:value-of select="@name"/>
    </h1>

    <br clear="all"/>
    <h2>Documentation</h2>
    <h3>
      Purpose: <xsl:value-of select="purpose"/>
    </h3>
    <xsl:apply-templates select="comment"/>
    
    <xsl:if test="count(see) &gt; 0">
      <br clear="all"/>
      <h2>See also</h2>
      <ul>
        <xsl:for-each select="see">
          <li>
            <xsl:apply-templates select="."/>
          </li>
        </xsl:for-each>
      </ul>
    </xsl:if>
    
    <br clear="all"/>
    <h2>Package contents</h2>
    <a name="__interfaces"/>
    <xsl:if test="count(class[@type = 'interface'])">
      <fieldset class="summary">
        <legend>Interface Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'interface']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__classes"/>
    <xsl:if test="count(class[@type = 'class'])">
      <fieldset class="summary">
        <legend>Class Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'class']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__enums"/>
    <xsl:if test="count(class[@type = 'enum'])">
      <fieldset class="summary">
        <legend>Enum Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'enum']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__exceptions"/>
    <xsl:if test="count(class[@type = 'exception'])">
      <fieldset class="summary">
        <legend>Exception Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'exception']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__errors"/>
    <xsl:if test="count(class[@type = 'error'])">
      <fieldset class="summary">
        <legend>Error Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'error']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>
