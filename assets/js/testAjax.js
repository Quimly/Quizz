$(function(){
	
	function callback(data){
		alert('Bonjour ' + data.username +'!');
	}
	
	$('#button').on('click',function(){	
		
		let pseudo = $('#textInput').val();
		$.post('/test/ajax2/',{username: pseudo}, callback);
		
	});
});