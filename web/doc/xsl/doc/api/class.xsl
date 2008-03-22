<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Shows a class' apidoc
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
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="doc.inc.xsl"/>

  <xsl:template match="class">
    <xsl:variable name="shortname" select="substring(@name, string-length(@package) + 2)"/>
    <div id="breadcrumb">
      <a href="{xp:link('api')}">API documentation</a> &#xbb;
      <xsl:call-template name="hierarchy">
        <xsl:with-param name="path" select="@package"/>
      </xsl:call-template>
       &#xbb;
      <a href="{xp:link(concat('api/class?', @name))}"><xsl:value-of select="$shortname"/></a>
    </div>
    <h1>
      <xsl:for-each select="modifiers/*">
        <xsl:value-of select="name()"/>
        <xsl:text> </xsl:text>
      </xsl:for-each>
      <xsl:value-of select="concat(@type, ' ', $shortname)"/>
    </h1>
    <p>
      <a><xsl:value-of select="@name"/></a>
      <xsl:for-each select="extends/link">
        &#xbb; <a href="?{@href}"><xsl:value-of select="@href"/></a>
      </xsl:for-each>
    </p>
    <xsl:if test="count(implements/link) &gt; 0">
      <h3>Implemented Interfaces</h3>
      <p>
        <xsl:for-each select="implements/link">
          <a href="?{@href}"><xsl:value-of select="@href"/></a>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
      </p>
    </xsl:if>

    <!-- Deprecation note -->
    <xsl:if test="deprecated">
      <fieldset class="warning">
        <p>
          <b>This class has been marked as deprecated.</b>
          Usage is discouraged though this class remains in the framework 
          for backward compatibility.<br/><br/>
          <em>
            <xsl:value-of select="deprecated" disable-output-escaping="yes"/>
          </em>
        </p>
      </fieldset>
    </xsl:if>

    <!-- Unittests -->
    <xsl:if test="count(test) &gt; 0">
      <fieldset class="hint">
        <p>
          This class' functionality is verified by the following tests:<br/>
          <xsl:for-each select="test">
            <xsl:variable name="class" select="substring-after(@href, 'xp://')"/>
            <a href="?{$class}"><xsl:value-of select="$class"/></a>
            <xsl:if test="position() != last()">, </xsl:if>
          </xsl:for-each>
        </p>
      </fieldset>
    </xsl:if>
    
    <!-- Final -->
    <xsl:if test="modifiers/final">
      <fieldset class="hint">
        <p>
          This class is declared as final - it cannot be overwritten!<br/>
          &#160;<br/>
        </p>
      </fieldset>
    </xsl:if>

    <!-- Abstract -->
    <xsl:if test="modifiers/abstract">
      <fieldset class="hint">
        <p>
          This class is declared as abstract - it must be overwritten!<br/>
          &#160;<br/>
        </p>
      </fieldset>
    </xsl:if>

    <!-- Apidoc -->
    <br clear="all"/>
    <h2>Documentation</h2>
    <h3>
      Purpose: <xsl:value-of select="purpose"/>
    </h3>
    <xsl:apply-templates select="comment"/>

    <!-- See also -->
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

    <!-- Constants -->
    <a name="__constants"/>
    <xsl:if test="count(constant) &gt; 0">
      <br clear="all"/>
      <h2>Constants</h2>
      <ul>
        <xsl:for-each select="constant">
          <li>
            <a name="{@name}"><b><xsl:value-of select="@name"/></b></a>
            <xsl:if test="string(.) != ''"><tt>= <xsl:copy-of select="func:cutstring(., 72)"/></tt></xsl:if>
          </li>
        </xsl:for-each>
      </ul>
    </xsl:if>
    
    <br clear="all"/>
    <h2>Members</h2>

    <!-- Fields -->
    <a name="__fields"/>
    <fieldset class="summary">
      <legend>Field summary</legend>
      <xsl:choose>
        <xsl:when test="count(fields[not(@from)]/field) &gt; 0">
          <h3>Fields declared in this class</h3>
          <ul>
            <xsl:for-each select="fields[not(@from)]/field">
              <li>
                <a name="{@name}"><b>
                  <xsl:for-each select="modifiers/*">
                    <xsl:value-of select="name()"/>
                    <xsl:text> </xsl:text>
                  </xsl:for-each>
                  <xsl:value-of select="@name"/>
                </b></a>
                <xsl:if test="string(constant) != ''"><tt>= <xsl:value-of select="constant"/></tt></xsl:if>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <em>(This class does not declare any fields)</em>
        </xsl:otherwise>
      </xsl:choose>

      <!-- Inherited fields -->
      <xsl:for-each select="fields[@from]">
        <xsl:if test="count(field) &gt; 0">
          <h3>Fields inherited from <a href="?{@from}"><xsl:value-of select="@from"/></a></h3>

          <p>
            <xsl:for-each select="field">
              <a href="?{../@from}#{@name}"><xsl:value-of select="@name"/></a>
              <xsl:if test="position() != last()">, </xsl:if>
            </xsl:for-each>
          </p>
        </xsl:if>
      </xsl:for-each>
    </fieldset>

    <!-- Methods -->
    <a name="__methods"/>
    <fieldset class="summary">
      <legend>Method summary</legend>
      <xsl:choose>
        <xsl:when test="count(methods[not(@from)]/method) &gt; 0">
          <h3>Methods declared in this class</h3>
          <ul>
            <xsl:for-each select="methods[not(@from)]/method">
              <li>
                <a href="#{@name}">
                  <xsl:value-of select="concat(@access, ' ', @return)"/>
                  <xsl:text> </xsl:text><b><xsl:value-of select="@name"/></b>
                  <xsl:text>(</xsl:text>
                  <xsl:for-each select="argument">
                    <xsl:value-of select="@name"/>
                    <xsl:if test="position() != last()">, </xsl:if>
                  </xsl:for-each>
                  <xsl:text>)</xsl:text>
                </a><br/>
                <em><xsl:value-of select="func:first-sentence(comment)" disable-output-escaping="yes"/></em>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:when>
        <xsl:otherwise>
          <em>(This class does not declare any methods)</em>
        </xsl:otherwise>
      </xsl:choose>

      <!-- Inherited methods -->
      <xsl:for-each select="methods[@from]">
        <xsl:if test="count(method) &gt; 0">
          <h3>Methods inherited from <a href="?{@from}"><xsl:value-of select="@from"/></a></h3>

          <p>
            <xsl:for-each select="method">
              <a href="?{../@from}#{@name}"><xsl:value-of select="@name"/>()</a>
              <xsl:if test="position() != last()">, </xsl:if>
            </xsl:for-each>
          </p>
        </xsl:if>
      </xsl:for-each>
    </fieldset>

    <br clear="all"/>
    <h2>Method details</h2>
    <xsl:for-each select="methods[not(@from)]/method">
      <a name="{@name}"/>
      <p class="annotations">
        <xsl:for-each select="annotations/annotation">
          <xsl:value-of select="concat('@', @name, '(', value, ')')"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        &#160;
      </p>
      <h4>
        <xsl:for-each select="modifiers/*">
          <xsl:value-of select="name()"/>
          <xsl:text> </xsl:text>
        </xsl:for-each>
        <xsl:text> </xsl:text>
        <a>
          <xsl:if test="contains(@return, '.')"><xsl:attribute name="href">
            <xsl:text>?</xsl:text>
            <xsl:value-of select="func:ltrim(substring-before(concat(@return, '['), '['), '&amp;')"/>
          </xsl:attribute></xsl:if>
          <xsl:value-of select="@return"/>
        </a>
        <xsl:text> </xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>(</xsl:text>
        <xsl:for-each select="argument">
          <xsl:value-of select="concat(@type, ' ', @name)"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
      </h4>
      <xsl:apply-templates select="comment"/>
      
      <xsl:if test="count(argument) &gt; 0">
        <h4>Arguments:</h4>
        <ul>
          <xsl:for-each select="argument">
            <li>
              <xsl:copy-of select="func:typelink(@type)"/>
              <xsl:text> </xsl:text>
              <xsl:value-of select="@name"/>
              <xsl:if test="string(default) != ''"><tt>= <xsl:value-of select="default"/></tt></xsl:if>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>

      <xsl:if test="count(exception) &gt; 0">
        <h4>Exceptions:</h4>
        <ul>
          <xsl:for-each select="exception">
            <li>
              <a href="?{@class}"><xsl:value-of select="@class"/></a>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>

      <xsl:if test="count(see) &gt; 0">
        <h4>See also</h4>

        <ul>
          <xsl:for-each select="see">
            <li>
              <xsl:apply-templates select="."/>
            </li>
          </xsl:for-each>
        </ul>
      </xsl:if>
      
      <hr/>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
