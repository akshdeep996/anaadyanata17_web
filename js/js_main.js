/*
    TABLE OF CONTENTS
    ---------------------------
     1. Loading
     2. Countdown
*/

/* ================================= */
/* :::::::::: 1. Loading ::::::::::: */
/* ================================= */
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
     $('.videobg').css({"opacity" : "0.0"});
  }
$(window).load(function() {

    $(".loader-icon").delay(500).fadeOut();
    $(".page-loader").delay(700).fadeOut("slow");




});

