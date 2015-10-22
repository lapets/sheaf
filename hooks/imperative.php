<?php /**************************************************************
**
** sheaf/hooks/imperative.php
** 
**   Hook for rendering imperative programming language programs in a
**   simplified language used for instructional purposes.
**
*/

/********************************************************************
*/

function sheaf_hook_imperative ($s) {
  $s = sheaf::r(trim($s), "\r", "");

  // Render as HTML.
  $s = sheaf::r($s, "\n", "<br/>");
  return $s;
}

/*eof*/ ?>