<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
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

  <xsl:include href="layout.xsl"/>
  <xsl:include href="../wizard.inc.xsl"/>
  
  <xsl:template name="context">
    <xsl:if test="/formresult/user">
      <xsl:call-template name="default_subnavigation">
        <xsl:with-param name="items">
          <xsl:for-each select="/formresult/eventtypes/type">
            <item href="{func:link(concat('events?', ., ',0,', /formresult/user/team_id))}"><xsl:value-of select="func:get_text(concat('common#next-', @type))"/></item>
          </xsl:for-each>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="content">
    <xsl:choose>
      <xsl:when test="/formresult/user">
        <xsl:copy-of select="func:box('success', 'Du bist angemeldet und hast Zugriff auf alle
          geschützten Bereiche der Webseite.')"/>
        
        <br/>
        <a href="{func:link('login?logout')}">Klicke hier, um dich wieder abzumelden!</a>
      </xsl:when>
      <xsl:otherwise>
        <p>
          Der angeforderte Bereich der Seite ist nur nach erfolgreicher Anmeldung zugänglich.<br/>
          Bitte gib hier deinen Usernamen und Passwort ein, um dich anzumelden:
        </p>

        <form method="post" action="{$__state}">
          <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'loginhandler']/@id}"/>
          
          <table>
            <tr>
              <td>Username:</td>
              <td><input type="text" name="username" value="{/formresult/formvalues/param[@name= 'username']}" size="20"/></td>
            </tr>
            <tr>
              <td>Passwort:</td>
              <td><input type="password" name="password" value="{/formresult/formvalues/param[@name= 'password']}" size="20"/></td>
            </tr>
            <tr>
              <td>&#160;</td>
              <td><input type="submit" name="submit" value="Anmelden"/></td>
            </tr>
          </table>
        </form>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
</xsl:stylesheet>
