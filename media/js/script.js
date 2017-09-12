jQuery(function ($){
	$(function(){
		/*$(document).on('click', '.menuitem_lnk', function(e) {
			e.preventDefault()
		})

		$('.menuitem_lnk').confirmModal({
			confirmTitle: '" . JText::_('COM_JAM_CONFIRM_TITLE') . "',
			confirmOk: '" . JText::_('COM_JAM_BUTTON_OK') . "',
			confirmCancel: '" . JText::_('COM_JAM_BUTTON_CANCEL') . "'
		});*/
                        
		$(document).on('click', '.menuitem_alias', function(e) {
			/*$('#alert-block').css('display', 'none');*/
	
			var articleId = $(this).data('article-id');
			var articleAlias = $(this).data('article-alias');
			var articleTitle = $(this).data('article-title');
                        
			$('#eaf_id_article').text( articleId );
			$('#eaf_title').text( articleTitle );	
			$('#eaf_alias').val( articleAlias );
                        
			$('#edit_alias_form').on('shown', function () {
				$('#eaf_alias').delay(1000).focus();
			});  
                        
			$('#edit_alias_form').modal();
                 
			$(document).on('click', '#eaf_btn_save', function(e) {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: 'index.php?option=com_jam&task=saveAlias',
					data: 'id_article=' + articleId + '&alias=' + $('#eaf_alias').val(),
					success: function(data){
						if (data.success == true){ 
							$('#edit_alias_form').modal('hide');
							
							var jmsgs = [data.message];
							var messages = { 'message': jmsgs };
							Joomla.renderMessages(messages);
										
							window.scrollTo(0, 0);         
						} 
					},
					error: function(){
						alert('failure');
					}
				});
			});
		});
	});
});
