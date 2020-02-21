jQuery(function($) {
    'use strict';

    var $toDoList = $('#todolist');
    var $timer;

    /**
     * Prevent form submission
     */
    $toDoList.submit(function(e) {
        e.preventDefault();
    });

    /**
     * Makes ajax request
     * Returns request to the listener
     */
    function ajaxAction(operation, id, value){
        return $.ajax({
            url : kutodolist_ajax.ajaxurl,
            dataType:  'json',
            type : 'post',
            data : ({
                action : 'todolist_handle',
                operation : operation,
                id : id,
                value : value,
                nonce : kutodolist_ajax.ajaxnonce
            })
        });
    }

    /**
     * Save ToDo data
     */
    $toDoList.keydown(function(e) {
        var $key = e.which;
        var $item = $(this).find(':focus').parent();

        if ( $key == 13 && $item.find('input[type="text"]').is(':focus') ) {
            if ( $item.data('id') == 0 ) {
                // Add
                if ( $item.find('input[type=text]').val().length != 0 ) {
                    ajaxAction('save', $item.data('id'), $item.find('input[type=text]').val()).done(function(response) {
                        if ( response.id != 'empty' ) {
                            $item
                                .clone()
                                .attr('data-id', response.id)
                                .appendTo($toDoList.find('ul'));

                            $toDoList.find('li[data-id="0"] input[type="text"]').val('');
                        }
                    });
                }
            }
            else {
                // Update
                $timer = setTimeout(function() {
                    ajaxAction('save', $item.data('id'), $item.find('input[type=text]').val()).done(function(response) {
                        if ( response.id == 'empty' ) {
                            $item.find('input[type=text]').val(response.value);
                        }
                    });
                }, 1000);
            }
        }
    });

    /**
     * Delete ToDo item
     */
    $toDoList.on('click', 'a', function(e) {
        e.preventDefault();

        var $item = $(this).parent();

        if ( $item.data('id') != 0 ) {
            ajaxAction('delete', $item.data('id')).done(function() {
                $item.addClass('deleted');

                setTimeout(function() {
                    $item.remove();
                }, 500);
            });
        }
    });

    /**
     * Check / uncheck
     * Mark ToDO item as complete
     */
    $toDoList.on('change', 'li input[type=checkbox]', function(e) {
       e.preventDefault();

        var $item = $(this).parent();

        if ( $item.data('id') != 0 ) {
            var $operation = 'uncheck';
            if ( $item.find('input[type=checkbox]').is(':checked') ) {
                $operation = 'check';
            }

            ajaxAction($operation, $item.data('id')).done(function() {
                $item.toggleClass('checked');
            });
        }
    });

    /**
     * Checkbox Handle
     * update checkbox status by clicking placeholder
     */
    $toDoList.on('click', 'span', function(e) {
        $(this).parent().find('input[type=checkbox]').trigger('click');
    });

});