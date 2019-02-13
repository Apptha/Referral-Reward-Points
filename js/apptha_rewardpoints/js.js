var $j_apptha = jQuery.noConflict();

$j_apptha(function(){
	$j_apptha("#transaction_box_hander").click(function(){
		$j_apptha("#transaction_history_box").slideToggle();
		if(this.innerHTML =='Hide') this.innerHTML = 'Show';
		else this.innerHTML = 'Hide';
	});
});