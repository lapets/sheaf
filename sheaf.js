/********************************************************************
**
** sheaf.js
**
**   A library that supports the representation and automated
**   rendering of lecture notes for mathematics and computer
**   science courses.
**
**   Web:     sheaf.io
**   Version: 0.0.1.0
**
**   JavaScript functions used for rendering of sheaf course
**   material XML files as HTML webpages.
*/

/********************************************************************
*/

$(document).ready(function() {
  $('.solution_toggle').click(function(e) {
    var sol = $(this).parent().next();
    $(this).text((sol.is(':visible')) ? 'show solution' : 'hide solution');
    sol.slideToggle();
  });
})

/*eof*/