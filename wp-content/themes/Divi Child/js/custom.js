var fees='';
jQuery(document).on('click', '#div_close', function(e){
	jQuery('#filter_div').slideUp(500);
});
jQuery(document).on('click', 'body', function(e){
// 	var fees= jQuery('#cfee').val();
// 	alert(fees)
// 	var objs=JSON.parse(fees);
// console.log(objs);
});
jQuery(document).on('click', '#ApplyFilters', function(e){
	jQuery('#filter_div').slideUp(500);
	jQuery('#filter_open').show();
	jQuery('#filtertext').text('Filters applied');
});

jQuery(document).on('click', '.deselectFilter', function(e){
	searchAllinThisloaction(0);
	jQuery('#filter_div').slideUp(500);
	jQuery('#filter_open').hide();
	jQuery('#filtertext').text('Filter changed. Search to apply.');
});

jQuery(document).on('click', '#btn_open', function(e){
	jQuery('#filter_div').slideToggle(500);
});
jQuery(document).on('click', '#autocomplete', function(e){
	jQuery('#autocomplete-error').hide();
});

function searchAllinThisloaction(search){
	$('#filter_div input:checkbox').each(function (index, element) {
		if ($(element).attr('id') == 'all_cats') {
			$(element).prop('checked', true);
		}else{
			$(element).prop('checked', false);
		}
	});
	$('#filter_div input[name="datefilter"]').each(function (index, element) {
		if ($(element).val() == 'nw') {
			$(element).prop('checked', true);
		}else{
			$(element).prop('checked', false);
		}
	});
	$('#filter_div input[name="pricefilter"]').each(function (index, element) {
		if ($(element).val() == 1) {
			$(element).prop('checked', true);
		}else{
			$(element).prop('checked', false);
		}
	});
	$('#myRange').val(50);
	$('.bubble').text('50 km');
	if(search==1){
		$('#SearchForm').submit();
	}
}
function performCatSearch(home_cid){
	$('#filter_div input:checkbox').each(function (index, element) {
		$(element).prop('checked', false);
	});
	$('.homecat_'+home_cid).prop('checked', true);
	$('#SearchForm').submit();
}

jQuery(document).ready(function () {
    //alert();
    jQuery('#streetaddress2').removeAttr('autocomplete');
    jQuery('#streetaddress2').prop('autocomplete', 'false');
    setTimeout(function(){
       // alert()
        jQuery('#streetaddress2').prop('autocomplete', 'none');
    }, 500)

	var limit = 8;
	jQuery('#myEvents li.event-detail:lt('+limit+')').show();
	jQuery(document).on('click', '#loadMore', function () {
		var total = jQuery(this).data('total');
		limit = parseInt(limit)+parseInt(8);
		jQuery('#myEvents li.event-detail:lt('+limit+')').show();

		if(parseInt(limit) > parseInt(total)) {
			jQuery('#loadMore').hide();
		}

	});

	jQuery("#event_form").submit(function(event){
		if($("#event_form").valid()){
			jQuery('#modal_loader_text').text('Saving event details and images...');
			jQuery('#loadingModal').show();
		}
	});
	jQuery("#tkt_form").submit(function(event){
		var thirdpartyurl = jQuery('#thirdpartyurl').val();
		var turl = jQuery('#turl').val();
		var fullurl = thirdpartyurl+turl;
		var regexp = /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/;

		if($("#tkt_form").valid()){

			if($("input[name=tkt_setup]:checked").val() == 'Yes 3rd party'){

				if(regexp.test(fullurl)) {
					jQuery('#turlerrorr').hide();
					jQuery('#modal_loader_text').text('Saving ticket details...');
					jQuery('#loadingModal').show();

				} else {
					console.log('testing inline');
					jQuery('#turlerrorr').text('Valid URL is required').css('display','inline-block');
					return false;
				}
			} else {

				jQuery('#modal_loader_text').text('Saving ticket details...');
				jQuery('#loadingModal').show();
			}

		}
	});

});
function updatePersonalInfo(){

	/* get all form data and send data to this file for updation */

	var site_url = jQuery('#Site_Url').val();
	var datastring = jQuery("#PerInfoForm").serialize();

	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/update-my-account-ajax.php',
			type: "post",
			data : datastring,
			success: function (result){

			}
	});
}

jQuery(document).ready(function() {

    jQuery('#psw').keyup(function(event) {
		var pswd = jQuery(this).val();
		validate(pswd);

	}).focus(function() {
		jQuery('#pswd_info').show();
	}).blur(function() {
		jQuery('#pswd_info').hide();
	});

});

function validate(pswd){
	//validate sapce
	if ( pswd.match(/\s/g) ) {
		jQuery('#space').removeClass('valid').addClass('invalid');
	} else {
		jQuery('#space').removeClass('invalid').addClass('valid');
	}

	if(pswd==''){
		// alert(pswd);
		jQuery('#space').removeClass('valid').addClass('invalid');
	}

	if ( pswd.length < 8 ) {
    jQuery('#length').removeClass('valid').addClass('invalid');
	} else {
		jQuery('#length').removeClass('invalid').addClass('valid');
	}

	//validate letter
	if ( pswd.match(/[a-z]/) ) {
		jQuery('#letter').removeClass('invalid').addClass('valid');
	} else {
		jQuery('#letter').removeClass('valid').addClass('invalid');
	}

	//validate capital letter
	if ( pswd.match(/[A-Z]/) ) {
		jQuery('#capital').removeClass('invalid').addClass('valid');
	} else {
		jQuery('#capital').removeClass('valid').addClass('invalid');
	}

	//validate number
	if ( pswd.match(/\d/) ) {
		jQuery('#number').removeClass('invalid').addClass('valid');
	} else {
		jQuery('#number').removeClass('valid').addClass('invalid');
	}
	//validate number
	if (passwordPreventCommon(pswd)) {
		jQuery('#comnpass').removeClass('valid').addClass('invalid');
	} else {
		jQuery('#comnpass').removeClass('invalid').addClass('valid');
	}

}

function passwordPreventCommon(commonValue) {

	var commonPassword = Array("123456","password","12345678","1234567890","1234","pussy","12345","dragon","qwerty","696969","mustang","letmein","baseball","master","michael","football","shadow","monkey","abc123","pass","6969","jordan","harley","ranger","iwantu","jennifer","hunter","2000","test","batman","trustno1","thomas","tigger","robert","access","love","buster","1234567","soccer","hockey","killer","george","sexy","andrew","charlie","superman","asshole","dallas","jessica","panties","pepper","1111","austin","william","daniel","golfer","summer","heather","hammer","yankees","joshua","maggie","biteme","enter","ashley","thunder","cowboy","silver","richard","orange","merlin","michelle","corvette","bigdog","cheese","matthew","121212","patrick","martin","freedom","ginger","blowjob","nicole","sparky","yellow","camaro","secret","dick","falcon","taylor","111111","131313","123123","bitch","hello","scooter","please","","porsche","guitar","chelsea","black","diamond","nascar","jackson","cameron","654321","computer","amanda","wizard","xxxxxxxx","money","phoenix","mickey","bailey","knight","iceman","tigers","purple","andrea","horny","dakota","aaaaaa","player","sunshine","morgan","starwars","boomer","cowboys","edward","charles","girls","booboo","coffee","xxxxxx","bulldog","ncc1701","rabbit","peanut","john","johnny","gandalf","spanky","winter","brandy","compaq","carlos","tennis","james","mike","brandon","fender","anthony","blowme","ferrari","cookie","chicken","maverick","chicago","joseph","diablo","sexsex","hardcore","666666","willie","welcome","chris","panther","yamaha","justin","banana","driver","marine","angels","fishing","david","maddog","hooters","wilson","butthead","dennis","captain","bigdick","chester","smokey","xavier","steven","viking","snoopy","blue","eagles","winner","samantha","house","miller","flower","jack","firebird","butter","united","turtle","steelers","tiffany","zxcvbn","tomcat","golf","bond007","bear","tiger","doctor","gateway","gators","angel","junior","thx1138","porno","badboy","debbie","spider","melissa","booger","1212","flyers","fish","porn","matrix","teens","scooby","jason","walter","cumshot","boston","braves","yankee","lover","barney","victor","tucker","princess","mercedes","5150","doggie","zzzzzz","gunner","horney","bubba","2112","fred","johnson","xxxxx","tits","member","boobs","donald","bigdaddy","bronco","penis","voyager","rangers","birdie","trouble","white","topgun","bigtits","bitches","green","super","qazwsx","magic","lakers","rachel","slayer","scott","2222","asdf","video","london","7777","marlboro","srinivas","internet","action","carter","jasper","monster","teresa","jeremy","11111111","bill","crystal","peter","pussies","cock","beer","rocket","theman","oliver","prince","beach","amateur","7777777","muffin","redsox","star","testing","shannon","murphy","frank","hannah","dave","eagle1","11111","mother","nathan","raiders","steve","forever","angela","viper","ou812","jake","lovers","suckit","gregory","buddy","whatever","young","nicholas","lucky","helpme","jackie","monica","midnight","college","baby","brian","mark","startrek","sierra","leather","232323","4444","beavis","bigcock","happy","sophie","ladies","naughty","giants","booty","blonde","golden","0","fire","sandra","pookie","packers","einstein","dolphins","0","chevy","winston","warrior","sammy","slut","8675309","zxcvbnm","nipples","power","victoria","asdfgh","vagina","toyota","travis","hotdog","paris","rock","xxxx","extreme","redskins","erotic","dirty","ford","freddy","arsenal","access14","wolf","nipple","iloveyou","alex","florida","eric","legend","movie","success","rosebud","jaguar","great","cool","cooper","1313","scorpio","mountain","madison","987654","brazil","lauren","japan","naked","squirt","stars","apple","alexis","aaaa","bonnie","peaches","jasmine","kevin","matt","qwertyui","danielle","beaver","4321","4128","runner","swimming","dolphin","gordon","casper","stupid","shit","saturn","gemini","apples","august","3333","canada","blazer","cumming","hunting","kitty","rainbow","112233","arthur","cream","calvin","shaved","surfer","samson","kelly","paul","mine","king","racing","5555","eagle","hentai","newyork","little","redwings","smith","sticky","cocacola","animal","broncos","private","skippy","marvin","blondes","enjoy","girl","apollo","parker","qwert","time","sydney","women","voodoo","magnum","juice","abgrtyu","777777","dreams","maxwell","music","rush2112","russia","scorpion","rebecca","tester","mistress","phantom","billy","6666","albert");

	if (commonPassword.includes(commonValue)) {
		return true;
	}
	return false;
}

/*
jQuery(document).on('change', '#title', function () {

	var val = jQuery(this).val();
	jQuery('#modal_event_title').text(val);
});

jQuery(document).on('change', '#venue', function () {

	var val = jQuery(this).val();
	jQuery('#modal_venue').text(val);
});

jQuery(document).on('change', '#address', function () {

    var val = jQuery(this).val();
    jQuery('#modal_address').text(val);
});

jQuery(document).on('change', '#country', function () {

	var val = jQuery(this).find(':selected').text();
	jQuery('#modal_country').text(val);
});

jQuery(document).on('change', '#state', function () {

	var val = jQuery(this).find(':selected').text();
	jQuery('#modal_province').text(val);
});

jQuery(document).on('change', '#city', function () {

	var val = jQuery(this).val();
	jQuery('#modal_city').text(val);

});

jQuery(document).on('change', '#postalcode', function () {

	var val = jQuery(this).val();
	jQuery('#modal_zip').text(val);
	initMap();
});

jQuery(document).on('change', '#description', function () {

	var val = jQuery(this).val();
	jQuery('#modal_description').text(val);
});

jQuery(document).on('change', '#category1_id', function () {
    $("#modal_catg").empty();
    var values = [];
    var $selectedOptions = $(this).find('option:selected');
    $selectedOptions.each(function(){
        values.push($(this).text());
    });
    $.each(values, function(index, value){
            $("#modal_catg").append('<div class="p-catg" style="margin-right:5px;">' + value + '</div>');
    });
});

jQuery(document).on('change', '#website_url', function () {

	var val = jQuery(this).val();
	jQuery('#modal_website').text(val);
});

jQuery(document).on('change', '#contact_name, #contact_name_check', function () {
	if($("#contact_name_check").is(":checked")){
		var val = jQuery("#contact_name").val();
		jQuery('#modal_name').text(val);
	}
	else{
		jQuery('#modal_name').text("");
	}
});

jQuery(document).on('change', '#contact_phone, #contact_phone_check', function () {
	if($("#contact_phone_check").is(":checked")){

		var val = jQuery("#contact_phone").val();
		jQuery('#modal_phone').text(val);
	}
	else{
		jQuery('#modal_phone').text("");
	}
});

jQuery(document).on('change', '#email_check, #email', function () {
	if($("#email_check").is(":checked")){
		var val = jQuery("#email").val();
		jQuery('#modal_email').text(val);
	}
	else{
		jQuery('#modal_email').text("");
	}
});

*/
function getUploadImageUrl(input, id) {

	var fileTypes = ['jpg', 'jpeg', 'png'];
	if (input.files && input.files[0]) {

		var extension = input.files[0].name.split('.').pop().toLowerCase();

		var reader = new FileReader();
		reader.onload = function (e) {
			if(e.total > 72000000 || fileTypes.indexOf(extension) <= -1) {
				jQuery("#"+id).parent().find('span').show();
			} else {
				jQuery("#"+id).parent().find('span').hide();
				jQuery("#"+id).attr("src",e.target.result);

				if(id=='fileToUpload_prev') {

					jQuery("#modal_image").attr("src",e.target.result);
				} else {

					jQuery("#modal_logo").attr("src",e.target.result);
				}
			}

		};
		reader.readAsDataURL(input.files[0]);
	}
}

function getUploadPdfUrl(input, id) {

	var fileTypes = ['pdf'];
	if (input.files && input.files[0]) {

		var extension = input.files[0].name.split('.').pop().toLowerCase();
		var filename = input.files[0].name;

		var reader = new FileReader();
		reader.onload = function (e) {
			if(e.total > 72000000 || fileTypes.indexOf(extension) <= -1) {
				jQuery("#"+id).parent().find('span').show();
			} else {
				jQuery("#"+id).parent().find('span').hide();
				// jQuery("#"+id).attr("src",e.target.result);
				jQuery("#"+id).hide();
				jQuery('#pdfname').show();
				jQuery('#pdfname').text(filename);
			}

		};
		reader.readAsDataURL(input.files[0]);
	}
}





jQuery('.select2-hidden-accessible').on('change', function() {
	if(jQuery(this).valid()) {
		jQuery(this).next('span').removeClass('error').addClass('valid');
	}
});

jQuery(document).on('change', '#category1_id', function () {

	jQuery(this).valid();
});

function dateCompare()
{
   
	var check_radio = jQuery("input:radio.check-radio:checked").val();

	//if(check_radio == '1') {

		if(jQuery('.single_start_date').val() != 'NOT SET' && jQuery('.single_end_date').val() != 'NOT SET')
		{

			var start_date = jQuery('.single_start_date').val();
			var start_date3 = new Date(start_date);
			var start_date4 = start_date3.getFullYear()+'-'+(start_date3.getMonth()+1)+'-'+start_date3.getDate();


			var end_date = jQuery('.single_end_date').val();
			var end_date3 = new Date(end_date);
			var end_date4 = end_date3.getFullYear()+'-'+(end_date3.getMonth()+1)+'-'+end_date3.getDate();
			var ampm = end_date3.getHours() >= 12 ? 'pm' : 'am';

			// previous date check start
			if(start_date3 > end_date3)
			{
				console.log("start is before after, resetting end to start date");
				jQuery('.single_end_date').val(jQuery('.single_start_date').val());
				var end_date = jQuery('.single_start_date').val();
				var end_date3 = new Date(end_date);
				var end_date4 = end_date3.getFullYear()+'-'+(end_date3.getMonth()+1)+'-'+end_date3.getDate();
				var ampm = end_date3.getHours() >= 12 ? 'pm' : 'am';
				//custom
			var start_date = jQuery('.single_start_date').val();
			jQuery('.single_end_date').val(start_date);
			//custom
			}
			//  previous date check ends

			if(start_date4 == end_date4)
			{

				if(check_radio == '1')
				{
					jQuery('#event_message').text('This event will start and end on the same date');
				}

				jQuery('#span_end_date').text('to '+end_date3.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).toLowerCase());
				jQuery('#prev_end_date').text('to '+end_date3.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).toLowerCase());
				//jQuery('#span_end_date').text('to '+end_date);
				//jQuery('#prev_end_date').text('to '+end_date);
			}
			else
			{
				if(check_radio == '1')
				{
					jQuery('#event_message').text('This event will start and end on a different date');
				}

				jQuery('#span_end_date').text('to '+end_date);
				jQuery('#prev_end_date').text('to '+end_date);
			}
		}
		else
		{
			if(check_radio == '1')
			{

				jQuery('#event_message').text('This event will start and end on a different date');
			}
		}
}

jQuery( function() {

	if(jQuery('#event_form').length)
	{
		jQuery('#event_form').validate({

			focusInvalid: false,
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				if (errors) {
					if(jQuery(validator.errorList[0].element).is(":visible"))
					{
						jQuery('html, body').animate({
							scrollTop: jQuery(validator.errorList[0].element).offset().top-250
						}, 1000);
					}
					else
					{
						jQuery('html, body').animate({
							scrollTop: jQuery("#" + jQuery(validator.errorList[0].element).attr("focusID")).offset().top
						}, 1000);
					}
				}
			},
			rules: {

				"event_start_date[]": {
					required: true,
					notset: true
				},

				"event_end_date[]": {
					required: true,
					notset: true
				},

				postalcode: {

					required: true,
					postalcode: true
				},

				extension: {

					number: true
				},
				"category_id[]": {

					required: true
				}
			},

			errorPlacement: function (error, element) {
				if (element.parent('.input-group').length) {
					error.insertAfter(element.parent());      // radio/checkbox?
				} else if (element.hasClass('select2-hidden-accessible')) {
					error.insertAfter(element.next('span'));  // select2
					element.next('span').addClass('error').removeClass('valid');
				} else if(element.hasClass('exclude_input')){
					error.insertAfter(element.next('span'));
				} else {
					error.insertAfter(element);               // default
				}
			}
		});
	}
	if ($('#category1_id').length)
		jQuery('#category1_id').select2();
	if ($('#contact_phone').length)
		jQuery('#contact_phone').mask('(000) 000-0000');
	if ($('#extension').length)
		jQuery('#extension').mask('000000000000000000000');


	var dateToday = new Date();
	setEndMinDate();
//var today = <?php date("F j, Y, g:i a"); ?>
	 //alert(dateToday);
   // var start_date = jQuery('.single_start_date').val(dateToday);
    //jQuery( ".start-date" ).trigger( "click" );
   // jQuery('#span_start_date').text(start_date);
//	jQuery('#span_end_date').text('');

	//if($('.single_start_date').length)
//	{	
if(jQuery(".create-event .single_start_date").val() == "NOT SET"){
		$.datetimepicker.setDateFormatter('moment');
		jQuery(".create-event .single_start_date").datetimepicker({
       //jQuery(".single_start_date").datetimepicker({

		/* minDate: 0, */
		/* minTime: 0, */
		/* minDateTime:0, */
		//minDateTime: new Date().toString,
		value: dateToday,
		minDateTime: new Date(),
		step: 15,
		validateOnBlur: false,
		//format:'M j, Y g:i a',
		//formatTime:	'g:i a',
		format: 'MMM D YYYY h:mm a',
		formatTime: 'h:mma',
		formatDate: 'YYYY-MM-DD',
		closeOnDateSelect:false,
		closeOnTimeSelect:true,
	//	onGenerate:function (e) {
		  //  setEndMinDate();
		    
//}
		onClose:function (e) {

			var start_date = jQuery('.single_start_date').val();
			//jQuery('.single_end_date').val(start_date);
			jQuery('#single_start_date-error').hide();
			jQuery('#single_end_date-error').hide();
			jQuery('#span_start_date').text(start_date);
			jQuery('#span_end_date').text('');
			/* jQuery('#prev_start_date').text(start_date); */
			/* dateCompare(); */
			setEndMinDate();
		}
		});
}
		if(jQuery(".create-event .single_start_date").val() != "NOT SET"){
		$.datetimepicker.setDateFormatter('moment');
		jQuery(".create-event .single_start_date").datetimepicker({
       //jQuery(".single_start_date").datetimepicker({

		/* minDate: 0, */
		/* minTime: 0, */
		/* minDateTime:0, */
		//minDateTime: new Date().toString,
		//value: dateToday,
		minDateTime: new Date(),
		step: 15,
		validateOnBlur: false,
		//format:'M j, Y g:i a',
		//formatTime:	'g:i a',
		format: 'MMM D YYYY h:mm a',
		formatTime: 'h:mma',
		formatDate: 'YYYY-MM-DD',
		closeOnDateSelect:false,
		closeOnTimeSelect:true,
	//	onGenerate:function (e) {
		  //  setEndMinDate();
		    
//}
		onClose:function (e) {

			var start_date = jQuery('.single_start_date').val();
			//jQuery('.single_end_date').val(start_date);
			jQuery('#single_start_date-error').hide();
			jQuery('#single_end_date-error').hide();
			jQuery('#span_start_date').text(start_date);
			jQuery('#span_end_date').text('');
			/* jQuery('#prev_start_date').text(start_date); */
			/* dateCompare(); */
			setEndMinDate();
		}
		});
		}
		
		//for edit
			jQuery(".edit_event .single_start_date").datetimepicker({


		/* minDate: 0, */
		/* minTime: 0, */
		/* minDateTime:0, */
		//minDateTime: new Date().toString,
	//	value: dateToday,
		minDateTime: new Date(),
		step: 15,
		validateOnBlur: false,
		//format:'M j, Y g:i a',
		//formatTime:	'g:i a',
		format: 'MMM D YYYY h:mm a',
		formatTime: 'h:mma',
		formatDate: 'YYYY-MM-DD',
		closeOnDateSelect:false,
		closeOnTimeSelect:true,
	//	onGenerate:function (e) {
		  //  setEndMinDate();
		    
//}
		onClose:function (e) {

			var start_date = jQuery('.single_start_date').val();
			//jQuery('.single_end_date').val(start_date);
			jQuery('#single_start_date-error').hide();
			jQuery('#single_end_date-error').hide();
			jQuery('#span_start_date').text(start_date);
			jQuery('#span_end_date').text('');
			/* jQuery('#prev_start_date').text(start_date); */
			/* dateCompare(); */
			setEndMinDate();
		}
		});
		//for edit end
//	}
//dateCompare();
	function setEndMinDate() {

		var start_date = jQuery('.single_start_date').val();
		var minDT = new Date(start_date);

		console.log('setEndMinDate: ' + minDT.toString());
		
		if(jQuery('#single_end_date').val() < jQuery('.single_start_date').val() ){
		jQuery('#single_end_date').attr("disabled",false);
     	jQuery('#single_end_date').val(jQuery('.single_start_date').val());
		}
     	
		$.datetimepicker.setDateFormatter('moment');
		jQuery('.single_end_date').datetimepicker({minDateTime: minDT, validateOnBlur: false, step: 15,
			//format:'M j, Y g:i a', formatTime:'g:i a',
			format: 'MMM D YYYY h:mm a',
			formatTime: 'h:mma',
			formatDate: 'YYYY-MM-DD',
			closeOnDateSelect:false, closeOnTimeSelect:true, onClose:function (e) {
				if(jQuery('.single_end_date').val() != 'NOT SET'){
					jQuery('#single_end_date-error').hide();
				}
				dateCompare();
			}});
			
	}

	// moved dateCompare out of here
	//if ($(".create-event .single_end_date").length)
	if ($(".create-event .single_end_date").val() == "NOT SET")
	{
		$.datetimepicker.setDateFormatter('moment');
		jQuery(".create-event .single_end_date").datetimepicker({
        value: dateToday,
		// minDate: '2019/12/29',
			step: 15,
			//formatTime:	'g:i a',
			//format:'M j, Y g:i a',
			format: 'MMM D YYYY h:mm a',
			formatTime: 'h:mma',
			formatDate: 'YYYY-MM-DD',
			closeOnDateSelect:false,
			closeOnTimeSelect:true,
			validateOnBlur: false,
			onClose:function (e) {
			if(jQuery(".single_end_date").val() != 'NOT SET'){
				jQuery('#single_end_date-error').hide();
			}
				dateCompare();
			}
		});
	}
	
		if ($(".create-event .single_end_date").val() != "NOT SET")
	{
		$.datetimepicker.setDateFormatter('moment');
		jQuery(".create-event .single_end_date").datetimepicker({
        //value: dateToday,
		// minDate: '2019/12/29',
			step: 15,
			//formatTime:	'g:i a',
			//format:'M j, Y g:i a',
			format: 'MMM D YYYY h:mm a',
			formatTime: 'h:mma',
			formatDate: 'YYYY-MM-DD',
			closeOnDateSelect:false,
			closeOnTimeSelect:true,
			validateOnBlur: false,
			onClose:function (e) {
			if(jQuery(".single_end_date").val() != 'NOT SET'){
				jQuery('#single_end_date-error').hide();
			}
				dateCompare();
			}
		});
	}
	
	//for edit event 
		if ($(".edit_event .single_end_date").length)
	{
		$.datetimepicker.setDateFormatter('moment');
		jQuery(".edit_event .single_end_date").datetimepicker({
       // value: dateToday,
		// minDate: '2019/12/29',
			step: 15,
			//formatTime:	'g:i a',
			//format:'M j, Y g:i a',
			format: 'MMM D YYYY h:mm a',
			formatTime: 'h:mma',
			formatDate: 'YYYY-MM-DD',
			closeOnDateSelect:false,
			closeOnTimeSelect:true,
			validateOnBlur: false,
			onClose:function (e) {
			if(jQuery(".single_end_date").val() != 'NOT SET'){
				jQuery('#single_end_date-error').hide();
			}
				dateCompare();
			}
		});
	}
	//for edit event
});



jQuery(document).on('click', '#check_multi', function () {

	jQuery('#add_more').show();
	jQuery('#event_message').text('This event will recur on multiple days including consecutive, weekly, monthly, etc.');
	//jQuery('#event_status_id').val('2');
});

jQuery(document).on('click', '#check_single', function () {
    jQuery('#event_message').text('This event will start and end on the same date');
	jQuery('#add_more').hide();
	jQuery('.add_more_div').hide();
	jQuery('.multi_span').remove();
	jQuery('.multi_start_date').remove();
	jQuery('.multi_end_date').remove();
	jQuery('#count').val('0');
	dateCompare();
});

jQuery(document).on('click', '#add_more', function (e) {

	e.preventDefault();

	var count = jQuery('#count').val();
	var inc_count = parseInt(1)+parseInt(count);
	jQuery('#count').val(inc_count);

	var html = '<div class="add_more_div" id="multi_div_'+inc_count+'">\n' +
		'                    <label style="cursor: pointer;" class="start-date" for="multi_start_date_'+inc_count+'">Start<input type="text" required class="start_datepicker multi_start_date" id="multi_start_date_'+inc_count+'" name="event_start_date[]" data-number="'+inc_count+'" value="NOT SET" placeholder="Select Start Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>\n' +
		'                    <label style="cursor: pointer;" class="start-date" for="multi_end_date_'+inc_count+'">End<input type="text" required class="end_datepicker multi_end_date" id="multi_end_date_'+inc_count+'" name="event_end_date[]" data-number="'+inc_count+'" value="NOT SET" placeholder="Select End Date/Time" style="cursor:pointer; padding: 0px !important; margin-bottom: 0px !important;"><small style="font-size: 12px !important; font-weight: normal;">Select to change</small></label>\n' +
		'                <span class="remove-date" style="cursor:pointer;" data-id="'+inc_count+'"> - </span></div>';

	jQuery(this).before(html);

	var span_date = '<p class="multi_span" id="p_'+inc_count+'"><span id="span_start_date_'+inc_count+'">NOT SET</span>&nbsp;<span id="span_end_date_'+inc_count+'">NOT SET</span></p>';
	var prev_date = '<p><span id="prev_start_date_'+inc_count+'">NOT SET</span>&nbsp;<span id="prev_end_date_'+inc_count+'">NOT SET</span></p>';
	jQuery('.evnt-dates').append(span_date);
	jQuery('.p-date').append(prev_date);
	$.datetimepicker.setDateFormatter('moment');
	var dateToday = new Date();
	var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);
   //setMultiEndMinDate();
		//	multiDateCompare();
	//var td = dateToday.setDate(dateToday.getDate()+1);
	jQuery("#multi_start_date_"+inc_count).datetimepicker({
        value:tomorrow,    
		//minDate: 0,
		//minTime: 0,
		minDateTime: new Date(today.getTime()+1000*60*60*24),
	
		validateOnBlur: false,
		step: 15,
		//format:'M j, Y g:i a',
		//formatTime:	'g:i a',
		format: 'MMM D YYYY h:mm a',
		formatTime: 'h:mma',
		formatDate: 'YYYY-MM-DD',
		closeOnDateSelect:false,
		closeOnTimeSelect:true,

		onClose:function (e) {

			var start_number = jQuery('#start').val();
			var start_date = jQuery("#multi_start_date_"+start_number).val();
			//jQuery("#multi_end_date_"+start_number).val(start_date);
			jQuery("#multi_start_date_"+start_number+"-error").hide();
			jQuery("#span_start_date_"+start_number).text(start_date);
			jQuery("#prev_start_date_"+start_number).text(start_date);
			setMultiEndMinDate(start_number);
			multiDateCompare(start_number);
		
			
		}

	});

	function multiDateCompare(start_number) {
       
		var start_number = start_number;
		//var start_date = jQuery("#multi_start_date_"+start_number).val();
		//var end_date = jQuery("#multi_end_date_"+start_number).val();

		if(jQuery("#multi_start_date_"+start_number).val() != 'NOT SET' && jQuery("#multi_end_date_"+start_number).val() != 'NOT SET') {
            var start_date = jQuery("#multi_start_date_"+start_number).val();
			var start_date3 = new Date(start_date);
			var start_date4 = start_date3.getFullYear()+'/'+(start_date3.getMonth()+1)+'/'+start_date3.getDate();
            
            var end_date = jQuery("#multi_end_date_"+start_number).val();
			var end_date3 = new Date(end_date);
			var end_date4 = end_date3.getFullYear()+'/'+(end_date3.getMonth()+1)+'/'+end_date3.getDate();
		//	var ampm = end_date3.getHours() >= 12 ? 'pm' : 'am';

// 			if(start_date4 >= end_date4) {
				jQuery("#multi_end_date_"+start_number).val(jQuery("#multi_start_date_"+start_number).val());
				
				var end_date = jQuery("#multi_start_date_"+start_number).val();
				var end_date3 = new Date(end_date);
				var end_date4 = end_date3.getFullYear()+'/'+(end_date3.getMonth()+1)+'/'+end_date3.getDate();
				//var ampm = end_date3.getHours() >= 12 ? 'pm' : 'am';
// 			}
			
			if(start_date4 == end_date4) {
				jQuery("#span_end_date_"+start_number).text('to '+end_date3.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).toLowerCase());
				jQuery("#prev_end_date_"+start_number).text('to '+end_date3.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }).toLowerCase());
			} else {

				jQuery("#span_end_date_"+start_number).text('to '+end_date);
				jQuery("#prev_end_date_"+start_number).text('to '+end_date);
			}

		}
	}

	function setMultiEndMinDate(start_number) {
        
		var start_number = start_number;
		var start_date = jQuery("#multi_start_date_"+start_number).val();
		
		var minDT = new Date(start_date);
		jQuery("#multi_end_date_"+start_number).val(jQuery("#multi_start_date_"+start_number).val());
		$.datetimepicker.setDateFormatter('moment');
	
		jQuery("#multi_end_date_"+start_number).datetimepicker({
           
		    //format:'M d, Y g:i a',
		    //formatTime:'g:i a',
				format: 'MMM D YYYY h:mm a',
				formatTime: 'h:mma',
				formatDate: 'YYYY-MM-DD',
		    minDateTime: minDT,
		    validateOnBlur: false,
		    value: start_date,
		    step: 15,
		    closeOnDateSelect:false,
		    closeOnTimeSelect:true,
		    closeOnDateSelect:false, closeOnTimeSelect:true, onClose:function (e) {
				if(jQuery("#multi_end_date_"+start_number).val() != 'NOT SET'){
				// 	jQuery("#multi_end_date_"+start_number+"-error").hide();
				    jQuery("#multi_end_date_"+start_number).val(convertDate(e));
                    
				    jQuery("#span_end_date_"+start_number).text('to '+convertDate(e).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }));
				jQuery("#prev_end_date_"+start_number).text('to '+convertDate(e).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }));
				}
				// multiDateCompare(start_number);
			}});
// 		    onClose:function (e) {
// 				// if(jQuery("#multi_end_date_"+start_number).val() != 'NOT SET'){
// 				// 	jQuery("#multi_end_date_"+start_number+"-error").hide();
// 				// }
// 				// multiDateCompare(start_number);
// 			}});
		
	}
 
var dateToday = new Date();

	//var td = dateToday.setDate(dateToday.getDate()+2);
// moved dateCompare out of here
	if ($(".multi_end_date").length > 0)
	{
	
	var tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    //console.log('Defence '+enddates);
    //var enddate =  jQuery("#multi_end_date_"+start_number).val(convertDate(e));
   let enddates = jQuery("#multi_start_date_"+inc_count).val();
   console.log(enddates);
//	$.datetimepicker.setDateFormatter('moment');
	jQuery("#multi_end_date_"+inc_count).datetimepicker({
	    	
		
	
		step: 15,
		//format:'M d, Y g:i a',
		//formatTime:	'g:i a',
		format: 'MMM D YYYY h:mm a',
		formatTime: 'h:mma',
		formatDate: 'YYYY-MM-DD',
		closeOnDateSelect:false,
		closeOnTimeSelect:true,
		validateOnBlur: false,
        value: enddates,
		onClose:function (e) {
		    
                //tomorrow.setDate(convertDate(e));
			var start_number = jQuery('#end').val();
			if(jQuery("#multi_end_date_"+start_number).val() != 'NOT SET'){
			jQuery("#multi_end_date_"+start_number+"-error").hide();
		}
			multiDateCompare(start_number);
		}
	});
}

function convertDate(date){
    let myDate = date.toLocaleString("en-US", {
        "month":"short", 
        "day":"numeric", 
        "year":"numeric",
    }).replace("," , ""); 
    let myTime = date.toLocaleString("en-US", {
        "timeStyle":"short", 
    }); 
    return `${myDate} ${myTime}`;
}


	jQuery('.multi_start_date').on('click', function (e) {

		var start_number = jQuery(this).data('number');
		jQuery('#start').val(start_number);
	})

	jQuery('.multi_end_date').on('click', function (e) {

		var end_number = jQuery(this).data('number');
		jQuery('#end').val(end_number);
	})

});

jQuery(document).on('click', '.remove-date', function () {

	var val = jQuery(this).data('id');
	var count = jQuery('#count').val();
	count = parseInt(count)-parseInt(1);
	jQuery('#p_'+val).remove();
	jQuery('#multi_div_'+val).remove();
	jQuery('#count').val(count);
});

//Code by libin

jQuery(document).on('change keypress', '.tqty', function(e){
	//var this =jQuery(this);
	CalculateSum(this);
});

function CalculateSum(mythis){
	var Qty= parseInt(jQuery(mythis).val() );
	if(Qty > 0){
		var tprice =parseFloat(jQuery(mythis).closest('tr').find('.tprice').val() );
		var ttax =parseFloat(jQuery(mythis).closest('tr').find('.ttax').val() );
		var tfee =parseFloat(jQuery(mythis).closest('tr').find('.tfee').val() );
	}
	else{
		var tprice =0;
		var ttax =0;
		var tfee =0;
	}

		var subTotal=(tprice+ttax+tfee)*Qty;
		jQuery(mythis).closest('tr').find('.ttoltxt').text(subTotal.toFixed(2));
		jQuery(mythis).closest('tr').find('.sub_total_each').val(subTotal.toFixed(2));
		//alert(subTotal)
		CalculateOverall();


}
function CalculateOverall(){
	var OverallTotal=0;
	var taxTotal=0;
	var feeTotal=0;
	var TicketTotal=0;
	var TicketQty=0;
	jQuery('.sub_total_each').each(function(){

		var tqty=parseInt($(this).closest('tr').find('.tqty').val());
		if (tqty > 0) {
			var ttotal=parseFloat($(this).closest('tr').find('.tprice').val());
			TicketTotal += parseFloat(ttotal*tqty);
			taxTotal += parseFloat($(this).closest('tr').find('.ttax').val());
			feeTotal += parseFloat($(this).closest('tr').find('.tfee').val());
			//alert($(this).val())
			OverallTotal += parseFloat($(this).val());
		}
	})
	$('.tkttotal').text('$'+ TicketTotal.toFixed(2));
	$('.tktTax').text('$'+ taxTotal.toFixed(2));
	$('.tktFee').text('$'+ feeTotal.toFixed(2));

	$('.Total').text('$'+ OverallTotal.toFixed(2));
}

//$('#element').click(function() {
 //  if($('#radio_single_1').is(':checked')) { $('.bundle-ticket-1').hide(); }
//});
