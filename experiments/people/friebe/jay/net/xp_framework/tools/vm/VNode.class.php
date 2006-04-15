<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.tools.vm.nodes.AnnotationNode',
    'net.xp_framework.tools.vm.nodes.AssignNode',
    'net.xp_framework.tools.vm.nodes.BinaryAssignNode',
    'net.xp_framework.tools.vm.nodes.BinaryNode',
    'net.xp_framework.tools.vm.nodes.BreakNode',
    'net.xp_framework.tools.vm.nodes.CaseNode',
    'net.xp_framework.tools.vm.nodes.CatchNode',
    'net.xp_framework.tools.vm.nodes.ClassConstantDeclarationListNode',
    'net.xp_framework.tools.vm.nodes.ClassConstantDeclarationNode',
    'net.xp_framework.tools.vm.nodes.ClassDeclarationNode',
    'net.xp_framework.tools.vm.nodes.ClassImportNode',
    'net.xp_framework.tools.vm.nodes.ClassReferenceNode',
    'net.xp_framework.tools.vm.nodes.ConstantReferenceNode',
    'net.xp_framework.tools.vm.nodes.ConstructorDeclarationNode',
    'net.xp_framework.tools.vm.nodes.DoWhileNode',
    'net.xp_framework.tools.vm.nodes.EchoNode',
    'net.xp_framework.tools.vm.nodes.ElseIfNode',
    'net.xp_framework.tools.vm.nodes.EnumMemberNode',
    'net.xp_framework.tools.vm.nodes.ExitNode',
    'net.xp_framework.tools.vm.nodes.FinallyNode',
    'net.xp_framework.tools.vm.nodes.ForNode',
    'net.xp_framework.tools.vm.nodes.ForeachNode',
    'net.xp_framework.tools.vm.nodes.FunctionCallNode',
    'net.xp_framework.tools.vm.nodes.FunctionDeclarationNode',
    'net.xp_framework.tools.vm.nodes.IfNode',
    'net.xp_framework.tools.vm.nodes.InterfaceDeclarationNode',
    'net.xp_framework.tools.vm.nodes.ImportListNode',
    'net.xp_framework.tools.vm.nodes.ImportNode',
    'net.xp_framework.tools.vm.nodes.MemberDeclarationListNode',
    'net.xp_framework.tools.vm.nodes.MemberDeclarationNode',
    'net.xp_framework.tools.vm.nodes.MethodCallNode',
    'net.xp_framework.tools.vm.nodes.MethodDeclarationNode',
    'net.xp_framework.tools.vm.nodes.NewClassNode',
    'net.xp_framework.tools.vm.nodes.NewNode',
    'net.xp_framework.tools.vm.nodes.NotNode',
    'net.xp_framework.tools.vm.nodes.ObjectReferenceNode',
    'net.xp_framework.tools.vm.nodes.OperatorDeclarationNode',
    'net.xp_framework.tools.vm.nodes.PackageDeclarationNode',
    'net.xp_framework.tools.vm.nodes.ParameterNode',
    'net.xp_framework.tools.vm.nodes.PostIncNode',
    'net.xp_framework.tools.vm.nodes.ReturnNode',
    'net.xp_framework.tools.vm.nodes.StaticMemberNode',
    'net.xp_framework.tools.vm.nodes.SwitchNode',
    'net.xp_framework.tools.vm.nodes.ThrowNode',
    'net.xp_framework.tools.vm.nodes.TryNode',
    'net.xp_framework.tools.vm.nodes.VariableNode',
    'net.xp_framework.tools.vm.nodes.WhileNode'
  );
  
  /**
   * Base class for all other nodes
   *
   */
  class VNode extends Object {
  }
?>
