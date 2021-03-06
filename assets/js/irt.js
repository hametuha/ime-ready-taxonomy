/**
 * Helper script for enhanced tag input
 *
 * @package IRT
 * @author Takahashi Fumiki
 * @since 1.0
 */

/*global IRT: true*/

jQuery(document).ready(function($){
    var
        /**
         * Returns taxonomy
         *
         * @param {Object} elt
         * @returns {String}
         */
        getTaxonomy = function(elt){
            return $(elt).parents('.tagsdiv').attr('id');
        },

        /**
         * Get Ajax URL
         *
         * @param {String }taxonomy
         * @returns {String}
         */
        buildURL = function(taxonomy){
            return IRT.endpoint + '?action=' + IRT.action + '&taxonomy=' + taxonomy +
                   "&" + IRT.nonceKey + '=' + IRT.nonceValue;
        },

        /**
         * Update tag value
         *
         * @param {Object} elt
         * @param {Object} input
         */
        updateTagValue = function(elt, input){
            var list = $(input).tokenInput('get'),
                textArea = $('#tax-input-' + getTaxonomy(elt)),
                chars = [];
            $.each(list, function(index, item){
                if(chars.indexOf(item.name)){
                    chars.push(item.name);
                }
            });
            textArea.val(chars.join(','));
        },

        /**
         * Add tag to list if it doesn't exists
         * @param {Object} target
         * @param {String} tag
         * @param {Number} id
         */
        addTagToList = function(target, tag, id){
            var list = $(target).tokenInput('get'),
                flg = true;
            $.each(list, function(index, elt){
               if(elt.name === tag){
                   flg = false;
                   return false;
               }
            });
            if(flg){
                $(target).tokenInput('add', {
                    id: id,
                    name: tag
                });
            }
        };

    // Add enhanced tag input to
    // each meta boxes.
    $('.jaxtag').each(function(index, elt){
        // hide default input
        var input = $('<input type="hidden" class="wpametu-newtag" id="newtag-' + getTaxonomy(elt) + '" value="" />'),
            taxonomy =  getTaxonomy(elt),
            inputId = '#token-input-newtag-' + taxonomy,
            currentChar = '',
            newTagCounter = 0,
            config = {
                theme: 'admin',
                preventDuplicates: true,
                tokenValue: 'name',
                hintText: IRT.hintText,
                noResultsText: IRT.noResultsText,
                searchingText: IRT.searchingText,
                resultsFormatter: function(item){
                    var str = item.name;
                    if(item.count >= 0){
                        str += '<small> (' + item.count + ')</small>';
                    }
                    return '<li>' + str + '</li>';
                },
                onReady: function(){
                    $(inputId).keyup(function(){
                        currentChar = this.value;
                    });
                },
                onAdd: function(item){
                    // Delete ID 0 and replace it with current char
                    if( !item.id ){
                        newTagCounter++;
                        $(input).tokenInput('remove', {id: 0});
                        addTagToList(input, currentChar, newTagCounter * -1);
                    }
                    updateTagValue(elt, input);
                },
                onDelete: function(){
                    // Update textarea value
                    updateTagValue(elt, input);
                }
            };


        // Do Ajax and retrieve tag ids
        $.ajax({
            type: 'GET',
            url: buildURL(taxonomy),
            data: {
                terms: $(elt).find('textarea.the-tags').val(),
                type: 'list'
            },
            dataType: 'json',
            success: function(result){
                // Now initializing
                if(result.length){
                    config.prePopulate = result;
                }
                input.insertBefore(elt);
                input.tokenInput(buildURL(taxonomy), config);
            }
        });


        // Bind tag cloud's click event
        var tagCloudId  = '#tagcloud-' + taxonomy,
            timer = setInterval(function(){
                if($(tagCloudId).length){
                    clearInterval(timer);
                    $(tagCloudId).find('a').click(function(){
                        var termID = parseInt($(this).attr('class').replace(/^[^0-9]+/, ''), 10),
                            term = $.trim($(this).text());
                        addTagToList(input, term, termID);
                        updateTagValue(elt, input);
                    });
                }
        }, 1000);
    });
});
