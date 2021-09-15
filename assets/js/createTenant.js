// Create Tenants by Ochiabuto Jideofor, Using Ajax to post data and call function to prevent long load times

$(document).ready(function() {
	
	$('#createTenant').click(function(){
		var companyName = $('input[name="companyName"]').val();
		var email = $('input[name="email"]').val();
		var phone = $('input[name="phone"]').val();
		var subdomainName = $('input[name="subdomainName"]').val();

		/**Check for empty input in order to run a preloader or return a message, 
		 * while the process is being run without the page loading 
		 * */
		if (companyName=='') {
			$('#companyNameError').html("Please enter a company name");
			$('#emailError').html('');
			$('#phoneError').html('');
			$('#subdomainNameError').html('');
		}else if (email=='') {
			$('#emailError').html("Enter Your Valid Email");
			$('#phoneError').html('');
			$('#subdomainNameError').html('');
			$('#companyNameError').html('');
		}else if(phone=='') {
			$('#phoneError').html("Enter Phone Number");
			$('#emailError').html('');
			$('#subdomainNameError').html('');
			$('#companyNameError').html('');
		}else if (subdomainName !='') {	
			$('#emailError').html('');
			$('#phoneError').html('');
			$('#subdomainNameError').html('');
			$('#companyNameError').html('');
			$('#message').html('Creating');
			// return;
			var dataString = $("#form").serialize();
			var url="Register/Register_Admin"
			$.ajax({
			type:"POST",
			url:url,
			data:dataString,
			success:function (data) {
				console.log(data);
				if(data){
					var response = data;
					if(!response.success){
						$('#emailError').html(response.emailError);
						$('#phoneError').html(response.phoneError);
						$('#subdomainNameError').html(response.subdomainNameError);
						$('#companyNameError').html(response.companyNameError);
						$('#message').html('');
					}
				}else{
					$('#message').html('Creating');
					$('#emailError').html('');
					$('#phoneError').html('');
					$('#subdomainNameError').html('');
					$('#companyNameError').html('');
				}
				// console.log(obj.emailError);
			}
			});  

		}else{
			$('#subdomainNameError').html("Enter Sub-domain name");
			$('#emailError').html('');
			$('#phoneError').html('');
			$('#companyNameError').html('');
		}
	});	
		
})

// function createTenant() {
// 	alert('Hello World');
// }
