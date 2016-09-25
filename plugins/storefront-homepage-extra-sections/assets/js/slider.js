//slider
jQuery(window).load(function() {
  jQuery('.flexslider').flexslider({
    animation: shesVars.shes_slider_options.animation,
    reverse: shesVars.shes_slider_options.reverse,
	//reverse: false,
	easing: "linear",
	animationSpeed: 2000,	
	pauseOnAction: true,
    pauseOnHover: true,
    directionNav: false,
    keyboard: true,
    touch: true,	
  });
});