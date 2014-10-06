jQuery(document).ready(function ($) {
//Adds audience button to menu
$(".audience-button")
.insertAfter("#ubc7-unit .btn-navbar");
//Moves category title above container div to reflect website layout
$(".category-title")
.insertBefore("#container");
//Moves the wiki embed table content to a side column
$("#toc")
.insertAfter(".tableoc");
$(".learn-more").click(function() {
	    $('html, body').animate({
	        scrollTop: $(".entry-content").offset().top
	    }, 500);
	});
});