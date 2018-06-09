require('../css/detailQuizz.css');

$(function() {
	
	$(".container-question").hide();
	$(".hide-details-question").hide();
	$(".hide-details-all-questions").hide();
	
	$(".container-answer").hide();
	$(".hide-details-answer").hide();
	$(".show-details-all-answers").hide();
	$(".hide-details-all-answers").hide();
	
	//__ Event des bouttons qui gerent les questions
	$(".show-details-question").on('click', function(e){
		$(this).parent().next(".container-question").show(500);
		$(this).hide();
		$(this).siblings(".hide-details-question").show();
		$(this).siblings(".show-details-all-answers").show();
		$(this).siblings(".hide-details-all-answers").trigger('click');
	});
	
	$(".hide-details-question").on('click', function(e){
		$(this).parent().next(".container-question").hide(250);
		$(this).hide();
		$(this).siblings(".show-details-question").show();
		$(this).siblings(".show-details-all-answers").hide();
		$(this).siblings(".hide-details-all-answers").hide();
	});
	
	$(".show-details-all-questions").on('click', function(e){
		let questions = $(this).parent().parent().parent().siblings("div.question-wrap").children("p").children("button.show-details-question");
		questions.trigger('click');
		$(this).siblings(".hide-details-all-questions").show();
		$(this).hide();
	});
	
	$(".hide-details-all-questions").on('click', function(e){
		let questions = $(this).parent().parent().parent().siblings("div.question-wrap").children("p").children("button.hide-details-question");
		questions.trigger('click');
		$(this).siblings(".show-details-all-questions").show();
		$(this).hide();
	});
	
	//__ Event des bouttons qui gerent les réponses
	$(".show-details-answer").on('click', function(e){
		$(this).parent().next(".container-answer").show(500);
		$(this).hide();
		$(this).siblings(".hide-details-answer").show();
		$(this).parent().parent().parent().parent().children("p").children(".hide-details-all-answers").show();
	});
	
	$(".hide-details-answer").on('click', function(e){
		$(this).parent().next(".container-answer").hide(250);
		$(this).hide();
		$(this).siblings(".show-details-answer").show();
		$(this).parent().parent().parent().parent().children("p").children(".show-details-all-answers").show();
	});
	
	$(".show-details-all-answers").on('click', function(e){
		let answers = $(this).parent().parent().children(".container-question").children(".answer-wrap").children("p").children("button.show-details-answer");
		answers.trigger('click');
		$(this).siblings(".show-details-all-answers").show();
		$(this).hide();
	});
	
	$(".hide-details-all-answers").on('click', function(e){
		let answers = $(this).parent().parent().children(".container-question").children(".answer-wrap").children("p").children("button.hide-details-answer");
		answers.trigger('click');
		$(this).siblings(".show-details-all-answers").show();
		$(this).hide();
	});
	
});