<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: attend.xsl 5768 2005-09-10 15:09:03Z kiesel $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="../layout.xsl"/>
  <xsl:include href="../../wizard.inc.xsl"/>
  
  <xsl:template name="context">
  </xsl:template>
  
  <xsl:template name="content">
    <form method="post" action="{func:link($__state)}">
    <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'accounteventhandler']/@id}"/>
    <input type="hidden" name="event_id" value="{/formresult/formvalues/param[@name= 'event_id']}"/>
    
    <xsl:variable name="status" select="/formresult/handlers/handler[@name= 'accounteventhandler']/@status"/>

    <xsl:copy-of select="func:display_wizard_error('accounteventhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('accounteventhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('accounteventhandler')"/>
    
    <xsl:if test="$status = 'initialized' or $status = 'setup' or $status = 'errors'">
    <div id="form">
      <fieldset>
        <legend>Punkteverteilung</legend>
        
        <table>
          <tr>
            <th>Spieler</th>
            <th>&#160;</th>
            <th>3 Punkte</th>
            <th>1 Punkt</th>
            <th>0 Punkte</th>
          </tr>
          
          <xsl:for-each select="/formresult/handlers/handler[@name= 'accounteventhandler']/values/players/player">
            <xsl:variable name="name" select="concat('points[player_', player_id, ']')"/>
            <tr>
              <td><xsl:value-of select="concat(firstname, ' ', lastname)"/></td>
              <td>&#160;</td>
              <td>
                <input type="radio" name="{$name}" value="3">
                  <xsl:if test="/formresult/formvalues/param[@name= $name] = 3"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
                </input>
              </td>

              <td>
                <input type="radio" name="{$name}" value="1">
                  <xsl:if test="/formresult/formvalues/param[@name= $name] = 1"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
                </input>
              </td>
              
              <td>
                <input type="radio" name="{$name}" value="0">
                  <xsl:if test="/formresult/formvalues/param[@name= $name] = 0"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
                </input>
              </td>
            </tr>
          </xsl:for-each>
        </table>
      </fieldset>
       
      <table>
        <tr>
          <td colspan="3" align="right">
            <input type="submit" name="submit" value="Abschicken!"/>
          </td>
        </tr>
      </table>
    </div>
    </xsl:if>
    </form>
  </xsl:template>
</xsl:stylesheet>
