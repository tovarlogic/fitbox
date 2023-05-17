jQuery(document).ready(function($){
	function animateMenuLine(object,duration)
	{
		if(object.length!=1) return;
		
		var width=jQuery(object).innerWidth();
		jQuery('.menu-menu-1-container .menu-line').stop().animate({left:parseInt(jQuery(object).position().left),width:width},{duration:duration,queue:false,complete:function() 
		{
				
		}});
	}
	jQuery(document.createElement('div')).attr('id','menu-line-top').addClass('menu-line').appendTo('.menu-menu-1-container');

	animateMenuLine(jQuery('ul.sf-menu').children('li.current-menu-item,li.current-menu-ancestor,li.current-page-ancestor'),0);

	jQuery('ul.sf-menu>li').hover(function() 
	{
		animateMenuLine(jQuery(this),250);
	});

	jQuery('ul.sf-menu').hover(function() 
	{

	},
	function()
	{
		animateMenuLine(jQuery(this).children('li.current-menu-item,li.current-menu-ancestor,li.current-page-ancestor'),250);
	});
	$(".button-go-to-top").click(function() {
		$("html, body").animate({scrollTop: 0}, "slow");
		return false;
	});
	//mobile menu
	$(".mobile_menu select").change(function(){
		window.location.href = $(this).val();
		return;
	});
	//style picker
	$(".style_picker_t1").change(function(){
		$(this).parent().parent().parent().find(".p_table_1").removeClass(function (index, css){
			return (css.match (/\p_table_1_\S+/g) || []).join(' ');
		}).addClass("p_table_1_" + $(this).val());
	});
	$(".style_picker_t2").change(function(){
		$(this).parent().parent().parent().find(".p_table_2").removeClass(function (index, css){
			return (css.match (/\p_table_2_\S+/g) || []).join(' ');
		}).addClass("p_table_2_" + $(this).val());
	});
	$(".style_picker option[selected='selected']").attr("selected", "selected");
});