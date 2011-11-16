jQuery ->
  $('ul.dropdown').css('position', 'absolute')
  $('ul.dropdown li.label').hide()
  $('ul.dropdown').hover(openSubmenu, closeSubmenu)
  top = 0
  maxWidth = 0
  for voice in $('ul.dropdown li:not(.label)')
    if $(voice).width() > maxWidth
      maxWidth = $(voice).width()
    $(voice).css('position', 'absolute')
    $(voice).css('left', '0')
    $(voice).css('background-color', 'white')
    $(voice).css('padding', '0.5em')
    if top == 0
      top = $(voice).outerHeight()
      $('ul.dropdown').css('margin-bottom', (top + 5) + 'px')
    if $(voice).children('a').hasClass('active')
      $(voice).css('top', 0)
      $(voice).css('border', '1px solid #CCC')
      $(voice).addClass('active')
    else
      $(voice).css('top', (top + 2) + 'px')
      $(voice).css('border-left', '1px solid #CCC')
      $(voice).css('border-right', '1px solid #CCC')
      $(voice).css('border-bottom', '1px solid #CCC')
      $(voice).hide()
      top += $(voice).outerHeight()

  for voice in $('ul.dropdown li:not(.label)')
    $(voice).width(maxWidth)


openSubmenu = ->
  $('ul.dropdown li:hidden').fadeIn()
closeSubmenu = ->
  $('ul.dropdown li:not(.active)').fadeOut()