jQuery(document).ready(function($) {
    var meta_image_frame;

    $(document).on('click', '.wvg-gallery-add-button', function(event){
        event.preventDefault ? event.preventDefault() : (event.returnValue = false);
    	var gallery = $(this).parent('p').parent('div');

        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: 'Choose image(s)',
            multiple: 'add',
            button: { text:  'Choose image(s)' },
            library: { type: 'image' }
        });

        meta_image_frame.on('select', function(){
            var selection = meta_image_frame.state().get('selection');
            var size = 'thumbnail';
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                var field = gallery.find('input');
                field.val(field.val() + attachment.id + ';');
                gallery.find('ul.wvg-gallery-images').append("<li data-id=\""+attachment.id+"\"><img src=" +attachment.sizes[size].url+ " /></li>");
            });
            gallery.find('input').trigger('change');
        });

        meta_image_frame.open();
    });

    //Remove image from input and list
    $(document).on('click', 'ul.wvg-gallery-images li', function(event){
    	event.preventDefault ? event.preventDefault() : (event.returnValue = false);
    	var attId = $(this).attr('data-id');
    	var gallery = $(this).parent('ul').parent('div');
    	$(this).remove();
    	var field = gallery.find('input').val();
    	field = field.replace(attId + ';', '');
    	gallery.find('input').val(field);
    	gallery.find('input').trigger('change');
    });

});