<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="exsl func"
>

  <xsl:output method="html" encoding="iso-8859-1"/>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="substring-before(concat(translate($comment, '&#10;', ' '), '. '), '. ')"/>
    </func:result>
  </func:function>

  <func:function name="func:ltrim">
    <xsl:param name="text"/>
    <xsl:param name="chars"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains(substring($text, 1, 1), $chars)">
          <xsl:value-of select="func:ltrim(substring($text, 2, string-length($text)), $chars)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$text"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <xsl:template match="class">
    <style type="text/css">
      h2 { margin-top: 30px; }
      h3 { margin-top: 20px; }
      h4 { font: bold 13px "Trebuchet MS", "Arial", sans-serif; margin-top: 0px; }
      hr { border: 1px solid #3165c5; height: 1px; }
      fieldset {
        margin-top: 20px;
        border: 1px solid #3165c5;
      }
      legend {
        font: bold 13px "Trebuchet MS", "Arial", sans-serif;
        color: #3165c5;
      }
      #content ul {
        list-style-type: square;
        list-style-image: url(image/li.gif);
        line-height: 18px;
      }
      code {
        display: block;
        white-space: pre;
      }
      p.annotations {
        font-family: "Lucida console", "Lucida", "Courier new", monospace;
        color: #3165c5;
        margin: 0px;
      }
      p.comment {
        color: #444444;
      }
    </style>
    <h1><xsl:value-of select="concat(@type, ' ', @name)"/></h1>

    <h2>Purpose: <xsl:value-of select="purpose"/></h2>
    
    <xsl:if test="deprecated">
      <em>
        Deprecated!
        <xsl:value-of select="deprecated" disable-output-escaping="yes"/>
      </em>
    </xsl:if>
    
    <p class="comment">
      <xsl:value-of select="comment" disable-output-escaping="yes"/>
    </p>
    
    <h2>Inheritance</h2>
    <p>
      <a><xsl:value-of select="@name"/></a>
      <xsl:for-each select="extends/link">
        &#xbb; <a href="?{@href}"><xsl:value-of select="@href"/></a>
      </xsl:for-each>
    </p>

    <xsl:if test="count(implements/link) &gt; 0">
      <h2>Implemented Interfaces</h2>
      <p>
        <xsl:for-each select="implements/link">
          <a href="?{@href}"><xsl:value-of select="@href"/></a>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
      </p>
    </xsl:if>

    <h2>Members</h2>

    <!-- Fields -->
    <fieldset>
      <legend>Field summary</legend>
      <xsl:choose>
        <xsl:when test="count(fields[not(@from)]/field) &gt; 0">
          <h3>Fields declared in this class</h3>
          <ul>
            <xsl:for-each select="fields[not(@from)]/field">
              <li>
                <a name="@name"><b><xsl:value-of select="@name"/></b></a>
                <xsl:if test="string(.) != ''"><tt>= <xsl:value-of select="."/></tt></xsl:if>
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
    <fieldset>
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
        <xsl:value-of select="@access"/>
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
          <xsl:value-of select="@name"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
      </h4>
      <p class="comment">
        <xsl:value-of select="comment" disable-output-escaping="yes"/>
      </p>
      
      <xsl:if test="count(argument) &gt; 0">
        <h4>Arguments:</h4>
        <ul>
          <xsl:for-each select="argument">
            <li>
              <xsl:value-of select="@name"/>
              <xsl:if test="string(.) != ''"><tt>= <xsl:value-of select="."/></tt></xsl:if>
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
      
      <hr/>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="/">
    <div id="search">
      <form action="/search">
        <label for="query"><u>S</u>earch XP website for </label>
        <input name="query" accesskey="s" type="text"></input>
      </form>
    </div>
    <div id="top">&#160;
    </div>
    <div id="menu">
      <ul>
        <li><a href="home.html">Home</a></li>
        <li><a href="news.html">News</a></li>
        <li id="active"><a href="?">Documentation</a></li>
        <li><a href="download.html">Download</a></li>
        <li><a href="dev.html">Developers</a></li>
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content">

        <xsl:apply-templates select="doc"/>
        
      </td>
      <td id="context">
        <h3>Further views</h3>
        <a href="#">Inheritance tree </a><br/>
      </td>
    </tr></table>
    <div id="footer">
      <a href="credits.html">Credits</a> |
      <a href="feedback.html">Feedback</a>
      
      <br/>
      
      (c) 2001-2006 the XP team
    </div>
  </xsl:template>
</xsl:stylesheet>
