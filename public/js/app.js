if($('#filterAdd')) {
	$('#filterAdd input[type^="radio"], #filterAdd select').on('change',function(){
		previewFilter('filterAdd');
	});
}
function previewFilter(id) {
	var formData=$( '#'+id+' form' ).serialize();
	$.post('filterpreview',formData,function(res){
		$( "#"+id+"List" ).html( res );
	});
}