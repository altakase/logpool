function disp_domain_warning(href){

    if(window.confirm('ドメインを削除してもよろしいですか？')){

        location.href = href;

    }

}

function disp_ssl_warning(href){

    if(window.confirm('SSL情報を削除してもよろしいですか？')){

        location.href = href;

    }

}

function disp_csr_warning(href){

    if(window.confirm('CSRが上書きされてしまいますが、よろしいですか？')){

        location.href = href;

    }
}

jQuery( function($) {
	$('tbody tr[data-href]').addClass('clickable').click( function() {
		window.location = $(this).attr('data-href');
	}).find('a').hover( function() {
		$(this).parents('tr').unbind('click');
	}, function() {
		$(this).parents('tr').click( function() {
			window.location = $(this).attr('data-href');
		});
	});
});
