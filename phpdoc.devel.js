
$(document).ready(function() {

var code,
	minlen = 2,	//lunghezza minima parola digitata
	t,
	element$ = $('#code'),
	tooltips$ = $('#tooltips');

element$.focus();

function logga(t) {
	$('#log').show().text(t)
}

$(document).bind('keydown',function (e) {
	if(e.keyCode!=27) return true; //tasto Esc
	tooltips.empty();
	element$.val('');
	$('#log').hide();
	return false;
});

$(document).bind('mouseover',function(e) {

	if($(e.target).is('#code'))
		$(e.target).focus();
});

$(':radio,:checkbox').bind('change',function(e) {
	element$.keypress();
});

element$.ajaxStart(function() {
	$(this).addClass('loading');
	$('#log').empty();
})
.ajaxStop(function() {
	$(this).removeClass('loading');
})
.click(function() {
	tooltips$.empty()
})
.keypress(function(e) {

  //e.preventDefault(); //non inserisce il carattere!!

  if(t) clearTimeout(t);  //pulisce il time precedente
  
  t = setTimeout(function() {
  
  code = element$.val();
  if(code=='' || code.length<minlen)
   return;
  
  $.getJSON('tooltips_json.php',
	{
	 func: code,
	 mode: $('#mode1').attr('checked') ? 'name': 'des',
	 dbtype: 'sqlite'//$('#sqlite').is(':checked') ? 'sqlite' : 'mysql'
	},
	function(resp) {

		tooltips$.empty();
					
		if(!resp.dati)
			return false;
		
		for(var r=0; r<resp.dati.length; r++)
		{
		  var linkphp = "http://php.net/manual/en/function."+resp.dati[r][0].replace(/_/g,"-")+".php";
		  
		  tooltips$.append('<div><b>'+resp.dati[r][0]+'</b>'+
			                    //'<i>'+resp.dati[r][3]+'</i>'+
			                    //' &gt; <strong>'+resp.dati[r][4]+'</strong>'+
			                    '<p>'+resp.dati[r][1]+'</p>'+
			                    '<em>'+resp.dati[r][2]+'</em>'+
			                    '<a href="'+linkphp+'">php.net &raquo;</a></div>');
		}
		logga(resp.dati.length +' risultati in '+ resp.time +'ms');
	});
		
  },300);

});

});
