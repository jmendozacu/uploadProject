define([
    'jquery',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'form',
    'validation'
], function (jQuery, mageTemplate, rg) {
    'use strict';

    return function (config) {

        var attributeOption = {
            table: $('attribute-tips-of-experts-table'),
            itemCount: 0,
            totalItems: 0,
            rendered: 0,
            template: mageTemplate('#tip-of-expert-template'),
            add: function (data, render) {
                var isNewOption = false, element;

                if (typeof data.id == 'undefined') {
                    data = {
                        'id': 'tip_' + this.itemCount,
                        'position': 0
                    };
                    isNewOption = true;
                }

                element = this.template({
                    data: data
                });

                if (isNewOption && !this.isReadOnly) {
                    this.enableNewOptionDeleteButton(data.id);
                }

                this.itemCount++;
                this.totalItems++;
                this.elements += element;

                if (render) {
                    this.render();
                    this.updateItemsCountField();
                    this.setLayoutJSON();
                }

                if (typeof data.list_block != 'undefined') {
                    $("list_block_" + data.id).setValue(data.list_block);
                }

                if (typeof data.expert_block != 'undefined') {
                    $("expert_block_" + data.id).setValue(data.expert_block);
                }
            },
            remove: function (event) {
                var element = $(Event.findElement(event, 'tr')), elementFlags; // !!! Button already have table parent in safari

                // Safari workaround
                element.ancestors().each(function (parentItem) {
                    if (parentItem.hasClassName('tip-row')) {
                        element = parentItem;
                        throw $break;
                    } else if (parentItem.hasClassName('box')) {
                        throw $break;
                    }
                });

                if (element) {
                    elementFlags = element.getElementsByClassName('delete-flag');

                    if (elementFlags[0]) {
                        elementFlags[0].value = 1;
                    }

                    element.addClassName('no-display');
                    element.addClassName('template');
                    element.hide();
                    this.totalItems--;
                    this.updateItemsCountField();
                    this.setLayoutJSON();
                }
            },
            enableNewOptionDeleteButton: function (id) {
                $$('#delete_button_container_' + id + ' button').each(function (button) {
                    button.enable();
                    button.removeClassName('disabled');
                });
            },
            render: function () {
                Element.insert($$('[data-role=tips-of-experts-container]')[0], this.elements);
                this.elements = '';
            },
            updateItemsCountField: function () {
                $('tip-count-check').value = this.totalItems > 0 ? '1' : '';
            },
            setLayoutJSON: function() {
                var layoutValues = new Array();
                jQuery('[data-role=tips-of-experts-container] tr').each(function (index, element) {
                    if(jQuery(this).find('.delete-flag').val() != '1') {
                        var element_obj = {
                            'id': 'tip_' + index,
                            'list_block': jQuery(this).find('.list_block').val(),
                            'expert_block': jQuery(this).find('.expert_block').val(),
                            'position': jQuery(this).find('.position').val()
                        };
                        layoutValues.push(element_obj);
                    }
                });
                jQuery('input[name=block_layout]').val(JSON.stringify(layoutValues)).trigger('change');
            }
        };
        
        if(jQuery('input[name=block_layout]').val() != undefined && jQuery('input[name=block_layout]').val() != "") {
            var layoutValues = jQuery('input[name=block_layout]').val();
            layoutValues = jQuery.parseJSON(layoutValues);
            jQuery(layoutValues).each(function(index, element) {
                attributeOption.add(element, true);
            });
        }

        if ($('add_new_tip_button')) {
            Event.observe('add_new_tip_button', 'click', attributeOption.add.bind(attributeOption, {}, true));
        }

        $('manage-tips-of-experts-panel').on('click', '.delete-tip', function (event) {
            attributeOption.remove(event);
        });

        $('manage-tips-of-experts-panel').on('change', '.list_block', function (event) {
            attributeOption.setLayoutJSON();
        });

        $('manage-tips-of-experts-panel').on('change', '.expert_block', function (event) {
            attributeOption.setLayoutJSON();
        });

        $('manage-tips-of-experts-panel').on('keyup', '.position', function (event) {
            attributeOption.setLayoutJSON();
        });

        $('manage-tips-of-experts-panel').on('focusout', '.position', function (event) {
            attributeOption.setLayoutJSON();
        });
    };
});