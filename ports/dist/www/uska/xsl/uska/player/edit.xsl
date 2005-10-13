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

  <xsl:include href="../layout.xsl"/>
  <xsl:include href="../../wizard.inc.xsl"/>
  
  <xsl:template name="context">
  </xsl:template>
  
  <xsl:template name="content">
    <form method="post" action="{func:link($__state)}">
    <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'editplayerhandler']/@id}"/>
    <input type="hidden" name="player_id" value="{/formresult/formvalues/param[@name= 'player_id']}"/>
    
    <xsl:variable name="status" select="/formresult/handlers/handler[@name= 'editplayerhandler']/@status"/>

    <xsl:copy-of select="func:display_wizard_error('editplayerhandler')"/>
    <xsl:copy-of select="func:display_wizard_success('editplayerhandler')"/>
    <xsl:copy-of select="func:display_wizard_reload('editplayerhandler')"/>
    
    <xsl:if test="$status = 'initialized' or $status = 'setup' or $status = 'errors'">
    <div id="form">
      <fieldset>
        <legend>Echter Name, Username und Passwort</legend>
        
        <!-- Firstname, lastname -->
        <table>
          <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'firstname')"/>
          <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'lastname')"/>
          
          <xsl:choose>
            <xsl:when test="/formresult/handler/values/mode = 'create' or '' != func:hasPermission('create_player')">
              <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'username')"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:copy-of select="func:wizard_row_print('editeventhandler', 'username')"/>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:copy-of select="func:wizard_row_password('editeventhandler', 'password')"/>
          <xsl:copy-of select="func:wizard_row_input('editeventhandler', 'email')"/>
        </table>
       </fieldset>
       
       <fieldset>
         <legend>Team + Position</legend>
         
          <!-- Team selection -->
          <table>
            <xsl:variable name="teams">
              <xsl:for-each select="/formresult/handlers/handler[@name= 'editplayerhandler']/values/teams/team">
                <option id="{team_id}"><xsl:value-of select="name"/></option>
              </xsl:for-each>
            </xsl:variable>
            <xsl:copy-of select="func:wizard_row_select('editeventhandler', 'team_id', $teams, 0)"/>

            <!-- Position and gender -->
            <xsl:variable name="positions">
              <option id="1">Torhüter</option>
              <option id="2">Abwehr</option>
              <option id="3">Mittelfeld</option>
              <option id="4">Sturm</option>
            </xsl:variable>
            <xsl:copy-of select="func:wizard_row_select('editeventhandler', 'position', $positions, 0)"/>
          </table>
        </fieldset>
        
      <table>
        <tr>
          <td colspan="3" align="right">
            <input type="submit" name="submit" value="Einstellungen speichern"/>
          </td>
        </tr>
      </table>
    </div>
    </xsl:if>
    </form>
  </xsl:template>
</xsl:stylesheet>
