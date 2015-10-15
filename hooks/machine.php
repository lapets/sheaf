<?php /**************************************************************
**
** sheaf/hooks/machine.php
** 
**   Hook for rendering machine language programs in a specific,
**   simplified machine language so that they can easily be
**   loaded into a simulator.
**
*/

/********************************************************************
*/

function sheaf_hook_machine ($s) {
  $s = sheaf::r(trim($s), "\r", "");

  // Build the simulator link.
  $instsURI = $s;
  $instsURI = sheaf::r($instsURI, "\n", "~");
  $instsURI = sheaf::r($instsURI, " ", "_");
  $instsURI = sheaf::r($instsURI, "#", "%23");
  $link = 'onclick="window.open(\'machine.php?instructions='.$instsURI.'\');"';

  // Highlight keywords and built-in functions.
  //$keywords = array('label', 'goto', 'branch', 'jump', 'set', 'copy', 'add');
  //$builtins = array('#increment#', '#decrement#', '#copy#');
  //foreach ($keywords as $t) {
  //  $s = sheaf::r($s, $t, '<span style="color:blue; font-weight:bold;">'.$t.'</span>');
  //foreach ($builtins as $t) {
  //  $s = sheaf::r($s, $t, '<span style="color:purple; font-weight:bold;">'.$t.'</span>');

  // Render as HTML.\
  $s = sheaf::r($s, "\n", "<br/>");
  $s = sheaf::r($s, " ", "&nbsp;");
  $s = '<div class="code"><div class="source"><br/>'.$s.'<br/></div><button '.$link.' style="margin-top:15px; width:100%;">simulator</button></div>';
  return $s;
}

/*eof*/ ?>