<?php

$toggle_speed = "'100'";

?>



jQuery(document).ready(function ($) {


  $('.samw-menu > li > a').click(function(){
    if ($(this).attr('class') != 'active'){
      $('.samw-menu li ul').slideUp(<?php echo $toggle_speed; ?>);
      $(this).next().slideToggle(<?php echo $toggle_speed; ?>);
      $('.samw-menu li a').removeClass('active');
      $(this).addClass('active');
    }
  });



  $('.sub-menu > li > a').click(function(){
    if ($(this).attr('class') != 'active'){
      $('.sub-menu li ul').slideUp(<?php echo $toggle_speed; ?>);
      $(this).next().slideToggle(<?php echo $toggle_speed; ?>);
      $('.samw-menu li a').removeClass('active');
      $(this).addClass('active');
    }
  });


  $(".current-menu-item").parents(".sub-menu").slideDown(<?php echo $toggle_speed; ?>);
  $(".current-menu-item").children(".sub-menu").slideDown(<?php echo $toggle_speed; ?>);




});