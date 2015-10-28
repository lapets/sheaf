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

  $s = sheaf::r($s, " ", "&nbsp;");

  // Highlight keywords and built-in functions.
  $keywords = array('label', 'goto', 'branch', 'jump', 'set', 'copy', 'add');
  $builtins = array('#increment#', '#decrement#', '#copy#');
  foreach ($keywords as $t)
    $s = sheaf::r($s, $t, '<span class="keyword">'.$t.'</span>');
  foreach ($builtins as $t)
    $s = sheaf::r($s, $t, '<span class="builtin">'.$t.'</span>');

  // Render as HTML.
  $s = sheaf::r($s, "\n", "<br/>");
  $s = '<div class="button"><button '.$link.'>simulator</button></div><div class="code" style="margin-top:0px; border-top:0px;"><div class="source"><br/>'.$s.'<br/><br/></div></div>';
  return $s;
}

/*eof*/ ?>