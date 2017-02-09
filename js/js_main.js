/*
    TABLE OF CONTENTS
    ---------------------------
     1. Loading
*/

/* ================================= */
/* :::::::::: 1. Loading ::::::::::: */
/* ================================= */
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    $('.videobg').css({
        "opacity": "0.0"
    });
}
$(window).load(function () {

    $(".loader-icon").delay(1000).fadeOut();
    $(".page-loader").delay(1500).fadeOut("slow");

});