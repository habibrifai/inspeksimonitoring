var ajax = function () 
{
	$.ajax({                                      
		url: 'php_handler.php',                     
		data: "",                             
		dataType: 'json',          
		success: function(data)          
		{
			// for (var i = 0; i < data.length; i++) {
				$('#output').html("<b>id: </b>"+data);
			// }
		} 
	});
}; 

setInterval(ajax, 1000 * 60 * 0.017);