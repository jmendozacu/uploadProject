(function($) {
    $.fn.easypin = function(options) {
        options = options || {};
        console.log(options);
        if (localStorage) {
            localStorage.removeItem('easypin');
        }
        var parentClass = $.fn.easypin.defaults.parentClass;
        var pinMapClass = $.fn.easypin.defaults.pinMapClass;
        var hoverClass = $.fn.easypin.defaults.hoverClass;
        var dashWidth = $.fn.easypin.defaults.dashWidth;
        var imageZindex = $.fn.easypin.defaults.imageZindex;
        var pinMapZindex = $.fn.easypin.defaults.pinMapZindex;
        var hoverLayerZindex = $.fn.easypin.defaults.hoverLayerZindex;
        $.extend($.fn.easypin.defaults, options);
        var willPinningElements = this;
        var loadedImgNum = 0;
        var total = $(this).length;
        willPinningElements.each(function(i) {
            $(this).css('opacity', 0);
        });
      /*  loadedImgNum += 1;
        $(this).animate({
            'opacity': '1'
        }, {
            duration: 'fast',
            easing: 'easeInQuad'
        });
        if (loadedImgNum == total) {
            willPinningElements.each(function(i) {
                var imageWidth = $(this).width();
                var imageHeight = $(this).height();
                if (imageHeight > 0) {
                    var containerElement = $(this).after($('<div/>', {
                        'class': parentClass
                    }).attr('data-index', setIndex(setClass(parentClass), document.body))).appendTo(setClass(parentClass) + ':last').css('position', 'absolute').css('z-index', imageZindex);
                    $(this).addClass('easypin-target')
                    if (!$(this).attr('easypin-id')) {
                        var easypinId = createRandomId();
                        $(this).attr('easypin-id', easypinId);
                    } else {
                        var easypinId = $(this).attr('easypin-id');
                    }
                    containerElement.parent().attr($.fn.easypin.config('widthAttribute'), imageWidth).attr($.fn.easypin.config('heightAttribute'), imageHeight).css({
                        width: setPx(imageWidth),
                        height: setPx(imageHeight),
                        position: $.fn.easypin.config('parentPosition'),
                        border: setPx(dashWidth) + ' dashed #383838',
                        'box-sizing': 'content-box',
                        'webkit-box-sizing': 'content-box',
                        '-moz-box-sizing': 'content-box'
                    });
                    initPin(easypinId, $(this));
                }
            });
            var parentElement = $(setClass(parentClass));
            $(parentElement).hover(function() {
                if (!is_open('popup', $(this))) {
                    $(this).prepend($('<div/>', {
                        'class': hoverClass
                    }).css({
                        width: '100%',
                        height: '100%',
                        position: 'absolute',
                        opacity: 0,
                        'z-index': hoverLayerZindex,
                        'background-color': 'black',
                        cursor: 'copy'
                    })).children(setClass(hoverClass)).animate({
                        opacity: 0.2,
                    }, 800);
                }
            }, function() {
                $(this).children(setClass(hoverClass)).animate({
                    opacity: 0,
                }, 'fast', 'swing', function() {
                    $(this).remove();
                });
            }).append($('<div/>', {
                'class': pinMapClass
            }).css({
                position: 'absolute',
                'z-index': pinMapZindex
            })).bind('mousedown', function(e) {
                if (!$(e.target).is(setClass(parentClass)) && !$(e.target).is(setClass(hoverClass))) {
                    e.stopPropagation();
                    return;
                }
                if (e.which != 1) return;
                var parentElement = e.currentTarget;
                var totalMarker = $('.easy-marker', parentElement).size();
                var limit = parseInt($.fn.easypin.defaults.limit);
                var elLimit = $('img' + setClass('easypin-target'), parentElement).attr('easypin-limit');
                if (elLimit && !isNaN(elLimit) && parseInt(elLimit) != 0) {
                    if (totalMarker >= parseInt(elLimit)) {
                        $.fn.easypin.defaults.exceeded('limit exceeded');
                        return;
                    }
                } else if (limit != 0 && totalMarker >= limit) {
                    $.fn.easypin.defaults.exceeded('limit exceeded');
                    return;
                }
                var imageWidth = $('img' + setClass('easypin-target'), parentElement).width();
                var imageHeight = $('img' + setClass('easypin-target'), parentElement).height();
                $(setClass(pinMapClass), parentElement).css({
                    width: setPx(imageWidth),
                    height: setPx(imageHeight)
                });
                var src = $.fn.easypin.defaults.markerSrc;
                var markerWidth = $.fn.easypin.defaults.markerWidth;
                var markerHeight = $.fn.easypin.defaults.markerHeight == 'auto' ? markerWidth : $.fn.easypin.defaults.markerHeight;
                var markerClass = $.fn.easypin.defaults.markerClass;
                var markerContainerZindex = $.fn.easypin.defaults.markerContainerZindex;
                var dashWidth = $.fn.easypin.defaults.dashWidth;
                var posYBalance = $.fn.easypin.defaults.posYBalance;
                var posXBalance = $.fn.easypin.defaults.posXBalance;
                var targetImage = $('img' + setClass('easypin-target'), parentElement);
                var imagePositionY = targetImage.offset().top - (dashWidth - posYBalance);
                var imagePositionX = targetImage.offset().left - (dashWidth - posXBalance);
                var clickPosX = (e.pageX - imagePositionX);
                var clickPosY = (e.pageY - imagePositionY);
                var markerWidthHalf = (markerWidth / 2);
                var markerHeightHalf = (markerHeight / 2);
                var markerBorderX = clickPosX - (markerWidth / 2);
                var markerBorderY = clickPosY - (markerHeight / 2);
                if (markerBorderX < 0) {
                    markerBorderX = 0;
                } else if (clickPosX + markerWidthHalf > imageWidth) {
                    markerBorderX = imageWidth - markerWidth;
                }
                if (markerBorderY < 0) {
                    markerBorderY = 0;
                } else if (clickPosY + markerHeightHalf > imageHeight) {
                    markerBorderY = imageHeight - markerHeight;
                }
                var absX = markerBorderX.toFixed(3) - markerWidthHalf;
                var absY = markerBorderY.toFixed(3) - markerHeightHalf;
                var tools = createTools({
                    markerWidth: markerWidth,
                    markerHeight: markerHeight
                });
                var markerContainer = createMarker({
                    tools: tools,
                    parentElement: parentElement,
                    markerClass: markerClass,
                    markerBorderX: markerBorderX,
                    markerBorderY: markerBorderY,
                    markerWidth: markerWidth,
                    markerHeight: markerHeight,
                    markerContainerZindex: markerContainerZindex,
                    markerWidth: markerWidth,
                    absX: absX,
                    absY: absY,
                    imageWidth: imageWidth,
                    imageHeight: imageHeight,
                    parentElement: parentElement,
                    src: src
                });
            });
            $.fn.easypin.di('instance', $.fn.easypin);
        }*/
        $(this).on('load', function() {
            loadedImgNum += 1;
            $(this).animate({
                'opacity': '1'
            }, {
                duration: 'fast',
                easing: 'easeInQuad'
            });
            if (loadedImgNum == total) {
                willPinningElements.each(function(i) {
                    var imageWidth = $(this).width();
                    var imageHeight = $(this).height();
                    if (imageHeight > 0) {
                        var containerElement = $(this).after($('<div/>', {
                            'class': parentClass
                        }).attr('data-index', setIndex(setClass(parentClass), document.body))).appendTo(setClass(parentClass) + ':last').css('position', 'absolute').css('z-index', imageZindex);
                        $(this).addClass('easypin-target')
                        if (!$(this).attr('easypin-id')) {
                            var easypinId = createRandomId();
                            $(this).attr('easypin-id', easypinId);
                        } else {
                            var easypinId = $(this).attr('easypin-id');
                        }
                        containerElement.parent().attr($.fn.easypin.config('widthAttribute'), imageWidth).attr($.fn.easypin.config('heightAttribute'), imageHeight).css({
                            width: setPx(imageWidth),
                            height: setPx(imageHeight),
                            position: $.fn.easypin.config('parentPosition'),
                            border: setPx(dashWidth) + ' dashed #383838',
                            'box-sizing': 'content-box',
                            'webkit-box-sizing': 'content-box',
                            '-moz-box-sizing': 'content-box'
                        });
                        initPin(easypinId, $(this));
                    }
                });
                var parentElement = $(setClass(parentClass));
                $(parentElement).hover(function() {
                    if (!is_open('popup', $(this))) {
                        $(this).prepend($('<div/>', {
                            'class': hoverClass
                        }).css({
                            width: '100%',
                            height: '100%',
                            position: 'absolute',
                            opacity: 0,
                            'z-index': hoverLayerZindex,
                            'background-color': 'black',
                            cursor: 'copy'
                        })).children(setClass(hoverClass)).animate({
                            opacity: 0.2,
                        }, 800);
                    }
                }, function() {
                    $(this).children(setClass(hoverClass)).animate({
                        opacity: 0,
                    }, 'fast', 'swing', function() {
                        $(this).remove();
                    });
                }).append($('<div/>', {
                    'class': pinMapClass
                }).css({
                    position: 'absolute',
                    'z-index': pinMapZindex
                })).bind('mousedown', function(e) {
                    if (!$(e.target).is(setClass(parentClass)) && !$(e.target).is(setClass(hoverClass))) {
                        e.stopPropagation();
                        return;
                    }
                    if (e.which != 1) return;
                    var parentElement = e.currentTarget;
                    var totalMarker = $('.easy-marker', parentElement).size();
                    var limit = parseInt($.fn.easypin.defaults.limit);
                    var elLimit = $('img' + setClass('easypin-target'), parentElement).attr('easypin-limit');
                    if (elLimit && !isNaN(elLimit) && parseInt(elLimit) != 0) {
                        if (totalMarker >= parseInt(elLimit)) {
                            $.fn.easypin.defaults.exceeded('limit exceeded');
                            return;
                        }
                    } else if (limit != 0 && totalMarker >= limit) {
                        $.fn.easypin.defaults.exceeded('limit exceeded');
                        return;
                    }
                    var imageWidth = $('img' + setClass('easypin-target'), parentElement).width();
                    var imageHeight = $('img' + setClass('easypin-target'), parentElement).height();
                    $(setClass(pinMapClass), parentElement).css({
                        width: setPx(imageWidth),
                        height: setPx(imageHeight)
                    });
                    var src = $.fn.easypin.defaults.markerSrc;
                    var markerWidth = $.fn.easypin.defaults.markerWidth;
                    var markerHeight = $.fn.easypin.defaults.markerHeight == 'auto' ? markerWidth : $.fn.easypin.defaults.markerHeight;
                    var markerClass = $.fn.easypin.defaults.markerClass;
                    var markerContainerZindex = $.fn.easypin.defaults.markerContainerZindex;
                    var dashWidth = $.fn.easypin.defaults.dashWidth;
                    var posYBalance = $.fn.easypin.defaults.posYBalance;
                    var posXBalance = $.fn.easypin.defaults.posXBalance;
                    var targetImage = $('img' + setClass('easypin-target'), parentElement);
                    var imagePositionY = targetImage.offset().top - (dashWidth - posYBalance);
                    var imagePositionX = targetImage.offset().left - (dashWidth - posXBalance);
                    var clickPosX = (e.pageX - imagePositionX);
                    var clickPosY = (e.pageY - imagePositionY);
                    var markerWidthHalf = (markerWidth / 2);
                    var markerHeightHalf = (markerHeight / 2);
                    var markerBorderX = clickPosX - (markerWidth / 2);
                    var markerBorderY = clickPosY - (markerHeight / 2);
                    if (markerBorderX < 0) {
                        markerBorderX = 0;
                    } else if (clickPosX + markerWidthHalf > imageWidth) {
                        markerBorderX = imageWidth - markerWidth;
                    }
                    if (markerBorderY < 0) {
                        markerBorderY = 0;
                    } else if (clickPosY + markerHeightHalf > imageHeight) {
                        markerBorderY = imageHeight - markerHeight;
                    }
                    var absX = markerBorderX.toFixed(3) - markerWidthHalf;
                    var absY = markerBorderY.toFixed(3) - markerHeightHalf;
                    var tools = createTools({
                        markerWidth: markerWidth,
                        markerHeight: markerHeight
                    });
                    var markerContainer = createMarker({
                        tools: tools,
                        parentElement: parentElement,
                        markerClass: markerClass,
                        markerBorderX: markerBorderX,
                        markerBorderY: markerBorderY,
                        markerWidth: markerWidth,
                        markerHeight: markerHeight,
                        markerContainerZindex: markerContainerZindex,
                        markerWidth: markerWidth,
                        absX: absX,
                        absY: absY,
                        imageWidth: imageWidth,
                        imageHeight: imageHeight,
                        parentElement: parentElement,
                        src: src
                    });
                });
                $.fn.easypin.di('instance', $.fn.easypin);
            }
        });
        return this;
    };
    var createMarker = function(depends) {
        var parentElement = depends.parentElement;
        var markerContainer = $('<div/>', {
            'class': depends.markerClass
        }).css('left', setPx(depends.markerBorderX)).css('top', setPx(depends.markerBorderY - 15)).css('width', depends.markerWidth).css('height', depends.markerHeight).css('margin-left', setPx(depends.marginLeft)).css('margin-top', setPx(depends.marginTop)).css('position', 'absolute').css('opacity', 0).css('z-index', depends.markerContainerZindex + 10).css('background-image', 'url(' + depends.src + ')').css('background-size', setPx(depends.markerWidth)).css('cursor', 'move').attr($.fn.easypin.config('xAttribute'), depends.absX).attr($.fn.easypin.config('yAttribute'), depends.absY).attr($.fn.easypin.config('widthAttribute'), depends.imageWidth).attr($.fn.easypin.config('heightAttribute'), depends.imageHeight).attr('data-index', setIndex('.easy-marker', depends.parentElement));
        var markerIndex = $(markerContainer).attr('data-index');
        var parentIndex = $(depends.parentElement).attr('data-index');
        var parentId = $('.easypin-target', depends.parentElement).attr('easypin-id');
        $(markerContainer).on('click', '.easy-delete', function(e) {
            dataRemove(parentId, markerIndex);
            $(e.currentTarget).closest('.easy-marker').remove();
        });
        $(markerContainer).on('click', '.easy-edit', function(e) {
            var modalInstance = createPopup(e, markerContainer);
            setDataToFields(parentId, markerIndex, modalInstance);
        });
        $(markerContainer).append(depends.tools);
        var xPosition = depends.markerBorderX.toFixed(3);
        var yPosition = depends.markerBorderY.toFixed(3);
        if (is_open('popup', depends.parentElement)) {
            $(depends.parentElement).prepend(markerContainer, $.fn.easypin.defaults.drop(depends.absX, depends.absY, markerContainer));
        } else {
            $(depends.parentElement).append(markerContainer, $.fn.easypin.defaults.drop(depends.absX, depends.absY, markerContainer));
        }
        if ((depends.markerBorderY + depends.markerHeight + 10) > depends.imageHeight) {
            var toolsPosition = -13;
        } else {
            var toolsPosition = depends.markerHeight + 2;
        }
        $(markerContainer).animate({
            opacity: 1,
            top: setPx(depends.markerBorderY)
        }, {
            duration: 'slow',
            easing: 'easeOutElastic',
            complete: function() {
                $(depends.tools).animate({
                    'opacity': '.4',
                    'top': setPx(toolsPosition)
                }, {
                    duration: 'slow',
                    easing: 'easeOutElastic'
                }).hover(function() {
                    $(this).animate({
                        'opacity': '1'
                    }, {
                        duration: 'slow',
                        easing: 'easeInOutQuint'
                    }).css('cursor', 'pointer');
                }, function() {
                    $(this).animate({
                        'opacity': '.4'
                    }, {
                        duration: 'slow',
                        easing: 'easeInOutQuint'
                    });
                });
            }
        });
        var cc = false;
        $(markerContainer).bind('mousedown', function(e) {
            e.stopPropagation();
            if (e.which != 1) return;
            var markerInstance = e.currentTarget;
            if ($(e.target).parent().is(setClass('popoverContent'))) {
                cc = true;
            } else {
                cc = $(e.target).is(setClass('popoverContent'));
            }
            $(parentElement).bind('mousemove', function(e) {
                if ((!$(e.target).is('div.easy-marker') && !$(e.target).is(setClass($.fn.easypin.defaults.hoverClass))) && cc === true) return;
                var parentElement = $(markerInstance).parent();
                var markerContainer = $(markerInstance);
                var targetImage = $('img.easypin-target', parentElement)
                var markerWidthHalf = (depends.markerWidth / 2);
                var markerHeightHalf = (depends.markerHeight / 2);
                liveY = e.pageY - targetImage.offset().top;
                liveX = e.pageX - targetImage.offset().left;
                var relY = liveY;
                var relX = liveX;
                if (liveY - markerHeightHalf < 0) {
                    var relY = markerHeightHalf;
                } else if (liveY + markerHeightHalf > depends.imageHeight) {
                    var relY = depends.imageHeight - markerHeightHalf;
                }
                if (liveX - markerWidthHalf < 0) {
                    var relX = markerWidthHalf;
                } else if (liveX + markerWidthHalf > depends.imageWidth) {
                    var relX = depends.imageWidth - markerWidthHalf;
                }
                var absX = relX.toFixed(3) - markerWidthHalf;
                var absY = parseInt(relY.toFixed(3)) + markerHeightHalf;
                checkToolsPosition(absY, depends.imageHeight, markerContainer)
                $.fn.easypin.defaults.drag(absX, absY, markerContainer);
                $(markerContainer).css({
                    position: 'absolute',
                    top: setPx(relY),
                    left: setPx(relX),
                    marginTop: -(depends.markerHeight / 2),
                    marginLeft: -(depends.markerWidth / 2),
                }).attr($.fn.easypin.config('xAttribute'), absX).attr($.fn.easypin.config('yAttribute'), absY);
            });
        });
        $(markerContainer).bind('mouseup', function(e) {
            cc = false;
            var markerInstance = e.currentTarget;
            var lat = $(markerInstance).attr($.fn.easypin.config('xAttribute'));
            var long = $(markerInstance).attr($.fn.easypin.config('yAttribute'));
            dataUpdate(parentId, markerIndex, {
                coords: {
                    lat: lat,
                    long: long
                }
            });
            $(parentElement).unbind('mousemove');
        });
        jQuery(".coords").trigger("click");
        return markerContainer;
    };
    var createTools = function(depends) {
        var tools = $('<div/>', {
            'class': 'easy-tools'
        }).css({
            'width': setPx(depends.markerWidth),
            'height': '10px',
            'position': 'absolute',
            'background-color': '#868585',
            'left': '-1px',
            'top': setPx((depends.markerHeight + 2) - 5),
            'opacity': '0'
        }).append(function() {
            return $('<a/>', {
                'class': 'easy-edit'
            }).css({
                'display': 'inline-block',
                'width': setPx(depends.markerWidth / 2),
                'height': '10px',
                'position': 'absolute',
                'left': '0px',
                'background-image': 'url(' + $.fn.easypin.defaults.editSrc + ')',
                'background-repeat': 'no-repeat',
                'background-size': '8px',
                'background-position-y': '1px',
                'background-position-x': '3px'
            }).hover(function() {
                $(this).css('background-color', 'black').css('opacity', '.6');
            }, function() {
                $(this).css('background-color', 'inherit');
            });
        }).append(function() {
            return $('<a/>', {
                'class': 'easy-delete'
            }).css({
                'display': 'inline-block',
                'width': setPx(depends.markerWidth / 2),
                'height': '10px',
                'position': 'absolute',
                'right': '0px',
                'background-image': 'url(' + $.fn.easypin.defaults.deleteSrc + ')',
                'background-repeat': 'no-repeat',
                'background-size': '8px',
                'background-position-y': '1px',
                'background-position-x': '3px'
            }).hover(function() {
                $(this).css('background-color', 'black').css('opacity', '.6');
            }, function() {
                $(this).css('background-color', 'inherit');
            });
        });
        return tools;
    };
    var checkToolsPosition = function(absY, imageHeight, markerContainer) {
        var markerHeight = $(markerContainer).height();
        var yBottom = absY + 10;
        if (yBottom > imageHeight && yBottom == imageHeight + 1) {
            $('.easy-tools', markerContainer).animate({
                'top': setPx(-13)
            }, {
                duration: 'slow',
                easing: 'easeOutElastic'
            });
        } else if ((absY - markerHeight) - 10 == -1) {
            $('.easy-tools', markerContainer).animate({
                'top': setPx(markerHeight + 2)
            }, {
                duration: 'slow',
                easing: 'easeOutElastic'
            });
        }
    };
    var getEventData = function(namespace) {
        switch (namespace) {
            case "get.coordinates":
                if (localStorage) {
                    return JSON.parse(localStorage.getItem('easypin'));
                } else {
                    try {
                        return JSON.parse(decodeURIComponent($('input[name="easypin-store"]').val()));
                    } catch (e) {
                        return null;
                    }
                }
                break;
            default:
                return null;
        }
    };
    getByIndex = function(index, data) {
        data = data || {};
        index = parseInt(index);
        if (typeof(data) == 'object') {
            var j = 0;
            for (var i in data) {
                j++;
                if (j == index) {
                    return data[i];
                }
            }
        }
        return {};
    };
    $.fn.easypinShow = function(options) {
        options = options || {};
        $.extend($.fn.easypinShow.defaults, options);
        try {
            var depends = {
                responsive: options.responsive || false,
                pin: options.pin || 'marker.png',
                data: options.data || {},
                popover: options.popover || {},
                error: typeof(options.error) != 'function' ? function(e) {} : options.error,
                each: typeof(options.each) != 'function' ? function(i, data) {
                    return data;
                } : options.each,
                success: typeof(options.success) != 'function' ? function() {} : options.success,
                allCanvas: this
            };
            var loadedImgNum = 0;
            var total = $(this).length;
            depends.allCanvas.each(function(i) {
                $(this).css('opacity', 0);
            });
            $(depends.allCanvas).one('load', function() {
                loadedImgNum += 1;
                $(this).animate({
                    'opacity': '1'
                }, {
                    duration: 'fast',
                    easing: 'easeInQuad'
                });
                if (loadedImgNum == total) {
                    var markerWidth = $(this)[0].width;
                    var markerHeight = $(this)[0].height;
                    var markerImgInstance = $(this);
                    pinLocate(depends);
                    depends.success.apply();
                }
            }).each(function() {
                if (this.complete) $(this).load();
            });
        } catch (e) {
            var args = new Array();
            args.push(e.message);
            args.push(e);
            depends.error.apply(null, args);
        }
    };
    pinLocate = function(depends) {
        $(depends.allCanvas).each(function(i) {
            var offsetTop = $(this).offset().top;
            var offsetLeft = $(this).offset().left;
            var canvas = $(this).parent();
            var height = $(this).height();
            var width = $(this).width();
            var parentWidth = $(canvas).width();
            if (depends.responsive === true) {
                var absWidth = '100%';
                var absHeight = '100%';
            } else {
                var absWidth = setPx(width);
                var absHeight = setPx(height);
            }
            var pinContainer = $('<div/>').css({
                'width': absWidth,
                'height': absHeight,
                'position': 'relative'
            }).addClass('easypin');
            $(this).css('position', 'relative').replaceWith(pinContainer);
            $(pinContainer).html($('<div/>').css('position', 'relative').css('height', '100%').append($(this)));
            var parentId = $(this).attr('easypin-id');
            if (typeof(depends.data) == 'string') {
                depends.data = JSON.parse(depends.data);
            }
            if (typeof(depends.data[parentId]) != 'undefined') {
                for (var j in depends.data[parentId]) {
                    if (j == 'canvas') return;
                    var tpl = $('[easypin-tpl]').clone();
                    $.fn.easypin.di('canvas_id', parentId);
                    $.fn.easypin.di('pin_id', j);
                    var args = new Array();
                    args.push(i);
                    args.push(depends.data[parentId][j]);
                    var returnData = depends.each.apply(null, args);
                    var viewContainer = viewLocater(depends.data[parentId], j, parentWidth, createView(returnData, tpl));
                    var opacity = getCssPropertyValue('opacity', $(viewContainer).clone());
                    $(viewContainer).css('opacity', 0);
                    $(pinContainer).append(viewContainer);
                    if (depends.popover.show == true) {
                        $('.easypin-popover', pinContainer).show();
                    }
                    $(viewContainer).animate({
                        'opacity': opacity
                    }, {
                        duration: 'slow',
                        easing: 'easeOutBack'
                    });
                    $('.easypin-marker:last', pinContainer).hover(function(e) {

                        var pos = $(this).position();   
                        var popPosition = 0;
                        if(jQuery(window).width() > 1400){
                            popPosition =  300;
                        }
                        if(jQuery(window).width() > 2500){
                            popPosition =  630;
                        }
                        
                        $(this).find('.easypin-popover').clone().insertAfter(".page-footer").css({
                            top: pos.top + 100 + "px",
                            left: pos.left + popPosition + "px",                            
                            'display': 'block',
                            'width': '200px',
                            'height': 'auto',
                            'opacity': '0',
                            'z-index': 14,
                            'background-color': 'transparent',
                        });

                        if ($.browser.mozilla) {
                           $('.page-footer').next('.easypin-popover').css({
                               'opacity': '1',
                           });
                        } 
                        
                        $('.page-footer').next('.easypin-popover').hover(function(e){
                                $('.page-footer').next('.easypin-popover').css({
                                    "display": "block"
                                });
                                
                                $(this).animate(
                                    { opacity: 1 }, {
                                        duration: 'slow',
                                        easing: 'easeOutBack'
                                    });                                 
                           
                        },function(e){
                            $('.page-footer').next('.easypin-popover').remove();
                        });             
                    
                    }, function() {
                        if ($('.page-footer').next('.easypin-popover').is(":hover")) {                            
                            $('.page-footer').next('.easypin-popover').css({
                                    "display": "block"
                                })
                        }
                        else{
                            setTimeOut(function(){
                                    $('.page-footer').next('.easypin-popover').remove();
                                
                            },10);                            
                        }                        
                    });
                    

                }
            }
        });
    };
    var createView = function(data, tplInstance) {
        var popover = $('popover', tplInstance);
        var marker = $('marker', tplInstance);
        $(popover).children(':first').addClass('easypin-popover').css('position', 'absolute').css('display', 'none');
        $(marker).children(':first').addClass('easypin-marker').css({
            'position': 'absolute'
        }); 
        
        var markerBorderWidth = $(marker).children(':first').css('border-width').replace('px', '');
        markerBorderWidth = markerBorderWidth != '' ? parseInt(markerBorderWidth) : 0;
        var markerWidth = $(marker).children(':first').width();
        var markerHeight = $(marker).children(':first').height();
        var popoverHeight = $(popover).children(':first').height();
        var popIns = $(popover).children(':first').clone();
        var bottom = getCssPropertyValue('bottom', popover);
        var newBottom = bottom == 'auto' ? setPx(markerHeight + markerBorderWidth) : bottom;
        $(popover).children(':first').css('top', newBottom).css('cursor', 'default');
        $(marker).children(':first').append(tplHandler(data, $(popover).html())).css('cursor', 'pointer');
        return $(marker).html();
    };
    var viewLocater = function(data, markerIndex, parentWidth, markerContainer) {
        var pinWidth = parseInt(data.canvas.width);
        var pinHeight = parseInt(data.canvas.height);
        var markerWidth = $(markerContainer).width();
        var markerHeight = $(markerContainer).height();
        var pos = calculatePinRate(data[markerIndex], pinWidth, markerWidth, pinHeight, markerHeight);
        var pinLeft = pos.left;
        var pinTop = pos.top;
        markerContainer = $(markerContainer).css('left', pinLeft + '%').css('top', pinTop + '%')
        return markerContainer;
    };
    var tplHandler = function(data, tpl) {
        if (typeof(data) == 'object') {
            var callbackVars = $.fn.easypinShow.defaults.variables;
            for (var i in data) {
                var content = data[i];
                if (typeof(callbackVars) != 'undefined' && typeof(callbackVars[i]) == 'function') {
                    var args = new Array();
                    args.push($.fn.easypin.container['canvas_id']);
                    args.push($.fn.easypin.container['pin_id']);
                    args.push(data[i]);
                    content = callbackVars[i].apply(null, args)
                }
                var pattern = RegExp("\\{\\[" + i + "\\]\\}", "g");
                tpl = tpl.replace(pattern, content);
            }
        }
        return tpl;
    };
    var calculatePinRate = function(data, pinWidth, markerWidth, pinHeight, markerHeight) {
        return {
            left: ((parseInt(data.coords.lat) + parseInt(8))/pinWidth)*100,
            top: ((parseInt(data.coords.long)-(markerHeight))/pinHeight)*100,
        };
    };
    var getCssPropertyValue = function(prop, el) {
        var el = $(el).hide().appendTo('body');
        var val = el.css(prop);
        el.remove();
        return val;
    };
    $.fn.easypin.clear = function() {
        if (localStorage) {
            localStorage.removeItem('easypin');
        } else {
            $('input[name="easypin-store"]').val('');
        }
    };
    $.fn.easypin.event = function(namespace, closure) {
        $.fn.easypin.di(namespace, closure);
    };
    $.fn.easypin.fire = function(namespace, params, callback) {
        if (typeof($.fn.easypin.container[namespace]) != 'undefined') {
            if (typeof(params) == 'function') {
                callback = params;
                params = null;
            } else {
                params = params || null;
                callback = callback || null;
            }
            if (typeof(callback) == 'function') {
                var callbackArgs = new Array();
                callbackArgs.push(getEventData(namespace));
                var eventReturn = callback.apply(null, callbackArgs);
            } else {
                var eventReturn = getEventData(namespace);
            }
            var dependsArgs = new Array();
            dependsArgs.push($.fn.easypin.container['instance']);
            dependsArgs.push(eventReturn);
            dependsArgs.push(params);
            $.fn.easypin.container[namespace].apply(null, dependsArgs);
        }
    };
    $.fn.easypin.config = function(attr) {
        return $.fn.easypin.defaults[attr];
    };
    $.fn.easypin.di = function(key, depends) {
        $.fn.easypin.container[key] = depends;
    };
    $.fn.easypin.call = function(func, params) {
        params = params || '';
        var depends = func.toString().match(/function\s*\(\s*(.*?)\s*\)/i);
        if (depends.length > 1) {
            depends = depends[1];
            var clientParm = depends.replace(/(\$[a-zA-Z]+)/g, '');
            clientParm = clientParm.replace(/\s+/g, '');
            clientParm = clientParm.replace(/,+/g, ',');
            clientParm = clientParm.replace(/(^,)/, '');
            clientParm = clientParm.split(/,/g);
            expectParm = depends.match(/(\$[a-zA-Z]+)/g);
            var dependsArgs = new Array();
            for (var i in expectParm) {
                if ($.fn.easypin.container[expectParm[i]]) {
                    dependsArgs.push($.fn.easypin.container[expectParm[i]]);
                }
            }
            dependsArgs.push(params);
            func.apply(null, dependsArgs);
        }
    };
    $.fn.easypin.defaults = {
        init: {},
        limit: 0,
        popover: {},
        exceeded: function() {},
        drop: function() {},
        drag: function() {},
        modalWidth: '200px',
        widthAttribute: 'data-width',
        heightAttribute: 'data-height',
        xAttribute: 'data-x',
        yAttribute: 'data-y',
        markerSrc: 'img/marker.png',
        editSrc: 'img/edit.png',
        deleteSrc: 'img/remove.png',
        parentClass: 'pinParent',
        markerClass: 'easy-marker',
        hoverClass: 'hoverClass',
        pinMapClass: 'pinCanvas',
        parentPosition: 'relative',
        popupOpacityLayer: 'popupOpacityLayer',
        markerWidth: 32,
        markerHeight: 'auto',
        animate: false,
        posYBalance: 2,
        posXBalance: 2,
        dashWidth: 2,
        imageZindex: 1,
        pinMapZindex: 2,
        hoverLayerZindex: 3,
        markerContainerZindex: 4,
        markerBorderColor: '#FFFF00',
        downPoint: 10
    };
    $.fn.easypinShow.defaults = {};
    $.fn.easypin.container = {};
    $.fn.easypin.markerContainer = {};
    var setClass = function(name) {
        return '.' + name;
    };
    var setPx = function(num) {
        return num + 'px';
    };
    var getMarkerUrl = function() {
        return $.fn.easypin.defaults.markerSrc;
    };
    var is_open = function(type, parentElement) {
        if (type == 'popup') {
            var className = setClass($.fn.easypin.defaults.popupOpacityLayer);
            return $(className, parentElement).size() > 0;
        }
    };
    var createPopup = function(elem, markerContainer) {
        var parentElement = $(elem.target).closest('.pinParent');
        var parentIndex = $(parentElement).attr('data-index');
        var targetImage = $('.easypin-target', parentElement);
        var widthAttr = $.fn.easypin.defaults.widthAttribute;
        var heightAttr = $.fn.easypin.defaults.heightAttribute;
        var opacityLayer = $('<div/>').addClass('popupOpacityLayer').css({
            'width': '100%',
            'height': '100%',
            'background-color': 'black',
            'position': 'absolute',
            'opacity': '.0',
            'z-index': 14
        });
        $(parentElement).append(opacityLayer).children(setClass($.fn.easypin.defaults.hoverClass)).hide().parent().children(setClass($.fn.easypin.defaults.popupOpacityLayer)).animate({
            opacity: 0.4,
        }, 800);
        var width = parseInt($(parentElement).attr(widthAttr));
        var height = parseInt($(parentElement).attr(heightAttr));
        var modalParent = $('<div/>').addClass('modalParent').css({
            'width': '100%',
            'height': '100%',
            'position': 'absolute',
            'z-index': 15
        }).click(function(e) {
            if ($(e.target).is('div.modalParent')) {
                closePopup(parentElement);
            }
            e.stopPropagation();
        });
        var modalContent = $('.easy-modal:last').clone();
        var modalContext = $('<div/>').addClass('modalContext').css({
            'background-color': '#fff',
            'width': $.fn.easypin.defaults.modalWidth,
            'opacity': '0',
            'position': 'absolute',
            'padding': '10px',
            '-webkit-box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)',
            '-moz-box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)',
            'box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)',
            '-webkit-border-radius': '5px',
            '-moz-border-radius': '5px',
            'border-radius': '5px',
            'cursor': 'move'
        }).append($(modalContent).show()).appendTo(modalParent);
        $('.popupOpacityLayer', parentElement).after(modalParent);
        var modalHeight = $('.modalContext').height()
        var modalWidth = $(modalContext).width();
        var parentLeft = $(elem.target).closest(setClass($.fn.easypin.defaults.parentClass)).offset().left;
        var markerLeft = $(elem.target).offset().left;
        var clickPosLeft = (markerLeft - parentLeft);
        var parentTop = $(elem.target).closest(setClass($.fn.easypin.defaults.parentClass)).offset().top;
        var markerTop = $(elem.target).offset().top;
        var clickPosTop = (markerTop - parentTop);
        if ($(modalContent).attr('modal-position') == 'free') {
            if ((clickPosLeft - 100) < modalWidth) {
                var modalLeftPosition = clickPosLeft + $(markerContainer).width() + 50;
            } else {
                var modalLeftPosition = clickPosLeft - modalWidth - 100;
            }
            if (modalHeight > height) {
                var modalTopPosition = parentTop;
            } else if ((height - clickPosTop) < modalHeight) {
                var modalTopPosition = height - (modalHeight + 100);
            } else {
                var modalTopPosition = clickPosTop - (modalHeight / 2);
            }
        } else {
            var modalLeftPosition = (width / 2) - (modalWidth / 2) - 10;
            var modalTopPosition = (height / 2) - ($(modalContext).height() / 2) - 10;
        }
        $(modalContext).css('top', -(modalHeight + 5) + 'px').css('left', modalLeftPosition + 'px');
        keyBinder(27, function() {
            if (is_open('popup', parentElement)) {
                closePopup(parentElement);
                $(document.body).unbind('keydown');
            }
        });
        $(parentElement).hover(function() {
            if ($(this).is(':hover') && is_open('popup', parentElement)) {
                keyBinder(27, function() {
                    closePopup(parentElement);
                });
            }
        }, function() {
            $(document).unbind('keydown');
        });
        $(modalContext).animate({
            'top': modalTopPosition + 'px',
            'opacity': '1'
        }, {
            duration: 'slow',
            easing: 'easeOutElastic'
        }).bind('mousedown', function(e) {
            if (!$(e.target).is('div.easy-modal') && !$(e.target).is('div.modalContext') && !$(e.target).is('form')) {
                e.stopPropagation();
                return;
            }
            var pinParent = $(e.currentTarget).closest('.pinParent');
            var downPageY = e.pageY - $(e.currentTarget).offset().top;
            var downPageX = e.pageX - $(e.currentTarget).offset().left;
            $(pinParent).bind('mousemove', function(e) {
                $(modalContext).css({
                    position: 'absolute',
                    top: setPx((e.pageY - parentElement.offset().top) - downPageY),
                    left: setPx((e.pageX - parentElement.offset().left) - downPageX)
                });
            });
        }).bind('mouseup', function(e) {
            var pinParent = $(e.currentTarget).closest('.pinParent');
            $(pinParent).unbind('mousemove');
        });
        $('.easy-submit', modalContext).click(function() {
            var lat = $(markerContainer).attr($.fn.easypin.defaults.xAttribute);
            var long = $(markerContainer).attr($.fn.easypin.defaults.yAttribute);
            var ImgWidth = $(markerContainer).attr($.fn.easypin.defaults.widthAttribute);
            var ImgHeight = $(markerContainer).attr($.fn.easypin.defaults.heightAttribute);
            var markerIndex = $(markerContainer).attr('data-index');
            var parentId = $('.easypin-target', parentElement).attr('easypin-id');
            var formExists = $('form', modalContext).size() > 0;
            if (formExists) {
                var modalBody = $('form', modalContext);
            } else {
                var modalBody = $('.easy-modal', modalContext);
            }
            if (typeof($.fn.easypin.defaults.done) == 'function') {
                var result = $.fn.easypin.defaults.done(modalBody);
                if (typeof(result) == 'boolean') {
                    if (result == true) {
                        closePopup(parentElement);
                    } else {
                        return;
                    }
                } else {
                    closePopup(parentElement);
                }
            }
            var formData = getFormData(modalBody, function(data) {
                data['coords'] = new Object();
                data.coords['lat'] = lat;
                data.coords['long'] = long;
                data.coords['canvas'] = new Object();
                data.coords.canvas['src'] = $(targetImage).attr('src');
                data.coords.canvas['width'] = ImgWidth;
                data.coords.canvas['height'] = ImgHeight;
                return data;
            });
            dataInsert(parentId, markerIndex, formData);
            createPopover(markerContainer, formData);
            jQuery(".coords").trigger("click");
        });
        return modalContext;
    };
    var dataInsert = function(parentId, markerIndex, data) {
        if (localStorage) {
            storageInsert(parentId, markerIndex, data);
        } else {
            inputInsert(parentId, markerIndex, data);
        }
    };
    var storageInsert = function(parentId, markerIndex, data) {
        var items = localStorage.getItem('easypin');
        if (!items) {
            var items = new Object();
        } else {
            try {
                var items = JSON.parse(items);
            } catch (e) {
                var items = new Object();
            }
        }
        var items = setNestedObject(parentId, markerIndex, items);
        if (typeof(items[parentId]['canvas']) == 'undefined') {
            items[parentId]['canvas'] = data.coords.canvas;
        }
        delete data.coords.canvas;
        if (typeof(data['canvas']) != 'undefined') {
            items[parentId]['canvas'] = data.canvas;
            delete data.canvas;
        }
        items[parentId][markerIndex] = data;
        localStorage.setItem('easypin', toJsonString(items));
    };
    var inputInsert = function(parentId, markerIndex, data) {
        var items = $('input[name="easypin-store"]').val();
        if (!items) {
            var items = new Object();
        } else {
            try {
                var items = JSON.parse(decodeURIComponent(items));
            } catch (e) {
                var items = new Object();
            }
        }
        var items = setNestedObject(parentId, markerIndex, items);
        if (typeof(items[parentId]['canvas']) == 'undefined') {
            items[parentId]['canvas'] = data.coords.canvas;
        }
        delete data.coords.canvas;
        if (typeof(data['canvas']) != 'undefined') {
            items[parentId]['canvas'] = data.canvas;
            delete data.canvas;
        }
        items[parentId][markerIndex] = data;
        if ($('input[name="easypin-store"]').size() < 1) {
            $(setClass($.fn.easypin.defaults.parentClass) + ':first').before('<input type="hidden" name="easypin-store" value="' + encodeURIComponent(toJsonString(items)) + '" />');
        } else {
            $('input[name="easypin-store"]').val(encodeURIComponent(toJsonString(items)));
        }
    };
    var dataUpdate = function(parentId, markerIndex, data) {
        if (localStorage) {
            storageUpdate(parentId, markerIndex, data);
        } else {
            inputUpdate(parentId, markerIndex, data);
        }
    };
    var storageUpdate = function(parentId, markerIndex, data) {
        var items = localStorage.getItem('easypin');
        if (items) {
            try {
                var items = JSON.parse(items);
                items = setNestedObject(parentId, markerIndex, items);
                items[parentId][markerIndex] = merge(items[parentId][markerIndex], data);
                localStorage.setItem('easypin', toJsonString(items));
            } catch (e) {
                return false;
            }
        }
    };
    var inputUpdate = function(parentId, markerIndex, data) {
        var items = $('input[name="easypin-store"]').val();
        if (items) {
            try {
                var items = JSON.parse(decodeURIComponent(items));
                items = setNestedObject(parentId, markerIndex, items);
                items[parentId][markerIndex] = merge(items[parentId][markerIndex], data);
                $('input[name="easypin-store"]').val(encodeURIComponent(toJsonString(items)));
            } catch (e) {
                return false;
            }
        }
    };
    var dataRemove = function(parentId, markerIndex) {
        if (localStorage) {
            removeFromStorage(parentId, markerIndex);
        } else {
            removeFromInput(parentId, markerIndex);
        }
    };
    var removeFromInput = function(parentId, markerIndex) {
        var items = $('input[name="easypin-store"]').val();
        if (items) {
            try {
                var items = JSON.parse(decodeURIComponent(items));
                items = removeHelper(parentId, markerIndex, items)
                var totalPin = $('input[name="easypin-store"]').size();
                if (totalPin < 1) {
                    $(setClass($.fn.easypin.defaults.parentClass) + ':first').before('<input type="hidden" name="easypin-store" value="' + encodeURIComponent(toJsonString(items)) + '" />');
                } else {
                    $('input[name="easypin-store"]').val(encodeURIComponent(toJsonString(items)));
                }
            } catch (e) {}
        }
    };
    var removeFromStorage = function(parentId, markerIndex) {
        var items = localStorage.getItem('easypin');
        if (items) {
            try {
                var items = JSON.parse(items);
                localStorage.setItem('easypin', toJsonString(removeHelper(parentId, markerIndex, items)));
            } catch (e) {}
        }
    };
    var removeHelper = function(parentId, markerIndex, items) {
        if (parentId && !markerIndex) {
            if (typeof(items[parentId]) != 'undefined') {
                delete items[parentId];
            }
        }
        if (parentId && markerIndex) {
            if (typeof(items[parentId][markerIndex]) != 'undefined') {
                delete items[parentId][markerIndex];
                if (sizeof(items[parentId]) < 1 || (sizeof(items[parentId]) == 1 && typeof(items[parentId]['canvas']) != 'undefined')) {
                    delete items[parentId];
                }
            }
        }
        return items;
    };
    var setNestedObject = function(parentId, markerIndex, items) {
        if (typeof(items[parentId]) == 'undefined') {
            items[parentId] = new Object();
        }
        if (typeof(items[parentId][markerIndex]) == 'undefined') {
            items[parentId][markerIndex] = new Object();
        }
        return items;
    };
    var getItem = function(parentIndex, markerIndex) {
        if (localStorage) {
            var items = localStorage.getItem('easypin');
            try {
                items = JSON.parse(items);
            } catch (e) {
                items = {};
            }
        } else {
            var items = $('input[name="easypin-store"]').val();
            try {
                items = JSON.parse(decodeURIComponent(items));
            } catch (e) {
                items = {};
            }
        }
        try {
            return items[parentIndex][markerIndex];
        } catch (e) {
            return null;
        }
    };
    var getFormData = function(element, callback) {
        var elements = new Object();
        $('input, select, textarea', element).each(function() {
            var elementType = $(this).attr('type');
            var elementName = $(this).attr('name');
            if (elementType == 'radio') {
                var checked = $(this).filter(":checked").val();
                if (typeof(checked) != 'undefined' && typeof(elements[elementName]) == 'undefined') {
                    elements[elementName] = checked;
                }
            } else if (elementType == 'checkbox') {
                if ($(this).is(':checked')) {
                    elements[elementName] = $(this).val();
                }
            } else {
                if (typeof($(this).val()) != 'undefined') {
                    elements[elementName] = $(this).val();
                }
            }
        });
        if (typeof(callback) == 'function') {
            var args = new Array();
            args.push(elements);
            return callback.apply(null, args);
        }
        return elements;
    };
    var toJsonString = function(data) {
        return JSON.stringify(data);
    };
    var closePopup = function(parentElement) {
        $(setClass($.fn.easypin.defaults.popupOpacityLayer), parentElement).animate({
            opacity: 0
        }, 'fast', 'swing', function() {
            $(this).remove();
        });
        var modalHeight = $('.modalContext', parentElement).height();
        var modalWidth = $('.modalContext', parentElement).width();
        $('.modalContext', parentElement).animate({
            'top': -(modalHeight + 50) + 'px',
            'opacity': '0',
            'z-index': 0
        }, {
            duration: 'slow',
            easing: 'easeOutElastic',
            complete: function() {
                $('.modalParent', parentElement).remove();
            }
        });
    };
    var keyBinder = function(expectCode, callback) {
        $(document).bind('keydown', function(e) {
            if (e.which == expectCode) {
                callback.apply(null);
                $(this).unbind('keydown');
            }
        });
    };
    var setIndex = function(selector, parent) {
        var index = $(selector + ':last', parent).attr('data-index');
        if (typeof(index) == 'undefined') {
            return '0';
        }
        return parseInt(index) + 1;
    };
    var sizeof = function(data) {
        if (typeof(data) == 'object') {
            var j = 0;
            for (var i in data) {
                j++;
            }
            return j;
        }
    };
    var setDataToFields = function(parentId, markerIndex, modalInstance) {
        var item = getItem(parentId, markerIndex);
        if (typeof(item) == 'object') {
            for (var i in item) {
                var element = $('[name="' + i + '"]', modalInstance);
                var type = $(element).prop('type');
                if (type == 'text' || type == 'hidden') {
                    $(element).attr('value', item[i]);
                } else if (type == 'checkbox') {
                    $(element).attr('checked', true);
                } else if (type == 'radio') {
                    $('[value="' + item[i] + '"]', modalInstance).attr('checked', true);
                } else if (type == 'textarea') {
                    $(element).val(item[i]);
                } else if (type == 'select-one') {
                    $(element).val(item[i]).prop('selected', true);
                }
            }
        }
    };
    var createPopover = function(markerContainer, formData) {
        var popoverCallBacks = sizeof($.fn.easypin.defaults.popover) > 0 ? $.fn.easypin.defaults.popover : false;
        var arrow = $('<div/>').addClass('popover-arrow-down').css({
            width: 0,
            height: 0,
            'border-left': '0px solid transparent',
            'border-right': '0px solid transparent',
            'border-top': '0px solid #000',
            'opacity': '.8',
            position: 'absolute',
            bottom: '-50px',
            left: '58px'
        });
        var tooltipContainer = $('<div/>').addClass('popover').css('position', 'absolute').css('display', 'inline').css('top', '-51px').css('left', '-51px').css('opacity', '0');
        var popoverHtml = $('[popover]:last').clone();
        popoverHtml.removeAttr('popover');
        var toHtml = popoverHtml.html();
        var popoverUserWidth = popoverHtml.attr('width') ? popoverHtml.attr('width') : 150;
        var defaultStyle = {
            color: '#FFFFFF',
            background: '#000000',
            opacity: '.8',
            height: 'auto',
            'line-height': '40px',
            'border-radius': '5px',
            cursor: 'context-menu'
        };
        if (typeof($.fn.easypin.defaults.popoverStyle) == 'object') {
            delete $.fn.easypin.defaults.popoverStyle.width;
            delete $.fn.easypin.defaults.popoverStyle.position;
            popoverStyle = merge(defaultStyle, $.fn.easypin.defaults.popoverStyle);
        } else {
            popoverStyle = defaultStyle;
        }
        var span = $('<span/>').addClass('popoverContent').css({
            position: 'absolute',
            width: setPx(popoverUserWidth)
        }).css(popoverStyle);
        var bgColor = $(span).css('background-color');
        $(arrow).css('border-top-color', bgColor);
        if (popoverHtml.attr('shadow') == 'true') {
            $(span).css({
                '-webkit-box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)',
                '-moz-box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)',
                'box-shadow': '10px 13px 5px 0px rgba(0,0,0,0.75)'
            });
        }
        delete formData['canvas'];
        for (var i in formData) {
            if (popoverCallBacks && typeof(popoverCallBacks[i]) == 'function') {
                var args = new Array();
                args.push(formData[i]);
                formData[i] = popoverCallBacks[i].apply(null, args);
            }
            var pattern = RegExp("\\{\\[" + i + "\\]\\}", "g");
            toHtml = toHtml.replace(pattern, formData[i]);
        }
        $(span).append(toHtml);
        $(tooltipContainer).append(span).append(arrow);
        if ($('div.popover', markerContainer).size() > 0) {
            $('div.popover', markerContainer).animate({
                'top': '-' + setPx((popoverHeight - 15)),
                'opacity': '0'
            }, {
                duration: 'slow',
                easing: 'easeOutElastic',
                complete: function() {
                    $(this).remove();
                }
            });
        }
        $(markerContainer).prepend(tooltipContainer);
        var popoverEl = $('div.popover > span:first', markerContainer);
        var popoverHeight = popoverEl.height();
        var popoverWidth = popoverEl.width();
        $(tooltipContainer).css('top', '-' + setPx((popoverHeight + 12) - 10)).css('left', '-' + setPx((popoverWidth / 2) - 16));
        $(arrow, tooltipContainer).css('left', setPx((popoverWidth / 2) - 10)).css('top', setPx(popoverHeight));
        $(tooltipContainer).animate({
            'top': '-' + setPx((popoverHeight + 12)),
            'opacity': '1'
        }, {
            duration: 'slow',
            easing: 'easeOutElastic'
        });
    };
    var initPin = function(imgIndex, element) {
        var initData = $.fn.easypin.defaults.init;
        var config = getConfigs();
        if (typeof(initData) == 'string') {
            initData = JSON.parse(initData)
        }
        var parentElement = $(element).parents(setClass(config.parentClass));
        if (sizeof(initData) > 0 && $(element).attr('easypin-init') != 'false') {
            var dashWidth = $.fn.easypin.defaults.dashWidth;
            var posYBalance = config.posYBalance;
            var posXBalance = config.posXBalance;
            var targetImage = $('img' + setClass('easypin-target'), parentElement);
            if (typeof(initData[imgIndex]) == 'undefined') {
                return;
            }
            for (var i in initData[imgIndex]) {
                if (isNaN(i) === true) return;
                var imageWidth = parseInt(initData[imgIndex].canvas.width);
                var imageHeight = parseInt(initData[imgIndex].canvas.height);
                var lat = parseInt(initData[imgIndex][i].coords.lat);
                var long = parseInt(initData[imgIndex][i].coords.long);
                var imagePositionY = targetImage.offset().top - (config.dashWidth - posYBalance);
                var imagePositionX = targetImage.offset().left - (config.dashWidth - posXBalance);
                var clickPosX = lat;
                var clickPosY = long;
                var markerWidthHalf = (config.markerWidth / 2);
                var markerHeightHalf = (config.markerHeight / 2);
                var markerBorderX = clickPosX - (config.markerWidth / 2);
                var markerBorderY = clickPosY - (config.markerHeight / 2);
                if (markerBorderX < 0) {
                    markerBorderX = 0;
                } else if (clickPosX + markerWidthHalf > imageWidth) {
                    markerBorderX = imageWidth - config.markerWidth;
                }
                if (markerBorderY < 0) {
                    markerBorderY = 0;
                } else if (clickPosY + markerHeightHalf > imageHeight) {
                    markerBorderY = imageHeight - config.markerHeight;
                }
                var absX = markerBorderX.toFixed(3) - markerWidthHalf;
                var absY = markerBorderY.toFixed(3) - markerHeightHalf;
                var tools = createTools({
                    markerWidth: config.markerWidth,
                    markerHeight: config.markerHeight
                });
                var markerContainer = createMarker({
                    tools: tools,
                    parentElement: parentElement,
                    markerClass: config.markerClass,
                    markerBorderX: markerBorderX,
                    markerBorderY: markerBorderY,
                    marginLeft: (config.markerWidth / 2),
                    marginTop: -(config.markerHeight / 2),
                    markerWidth: config.markerWidth,
                    markerHeight: config.markerHeight,
                    markerContainerZindex: config.markerContainerZindex,
                    absX: absX,
                    absY: absY,
                    imageWidth: imageWidth,
                    imageHeight: imageHeight,
                    parentElement: parentElement,
                    src: config.src
                });
                var markerIndex = $(markerContainer).attr('data-index');
                var markerData = merge(initData[imgIndex][i], {
                    'canvas': initData[imgIndex]['canvas']
                });
                dataInsert(imgIndex, markerIndex, markerData);
                createPopover(markerContainer, initData[imgIndex][i]);
            }
        }
    };
    var createRandomId = function() {
        var length = 10;
        var strings = 'abcdefghijklmnoprstuvyzABCDEFGHIJKLMNOPRSTUVYZ1234567890_';
        var key = '';
        for (var i = 1; i <= length; i++) {
            var randNum = Math.floor(Math.random() * (strings.length - 1 + 1) + 1);
            if (typeof(strings[randNum]) != 'undefined') {
                key += strings[randNum];
            }
        }
        return key;
    };
    var getConfigs = function() {
        return {
            src: $.fn.easypin.defaults.markerSrc,
            markerWidth: $.fn.easypin.defaults.markerWidth,
            markerHeight: $.fn.easypin.defaults.markerHeight == 'auto' ? $.fn.easypin.defaults.markerWidth : $.fn.easypin.defaults.markerHeight,
            markerClass: $.fn.easypin.defaults.markerClass,
            parentClass: $.fn.easypin.defaults.parentClass,
            markerContainerZindex: $.fn.easypin.defaults.markerContainerZindex,
            dashWidth: $.fn.easypin.defaults.dashWidth,
            posYBalance: $.fn.easypin.defaults.posYBalance,
            posXBalance: $.fn.easypin.defaults.posXBalance,
            dashWidth: $.fn.easypin.defaults.dashWidth
        };
    };

    function merge(obj1, obj2) {
        for (var p in obj2) {
            try {
                obj1[p] = obj2[p];
            } catch (e) {
                obj1[p] = obj2[p];
            }
        }
        return obj1;
    }
}(jQuery));