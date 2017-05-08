var pcgfSuggestionList = $('#pcgf-namesuggestion-list');
var pcgfSelectors = pcgfNameSuggestionSelectors.split(';');
var pcgfCurrentElement = null;
var pcgfLastSearchValue = '';
var pcgfResultCount = 0;
var pcgfLastSelectedIndex = -1;
var pcgfKeyCatched = 0;

function hideSuggestions() {
    // Hide the suggestion list
    pcgfSuggestionList.css('display', 'none');
    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').removeClass('selected');
    pcgfLastSelectedIndex = -1;
}

function setSuggestionPosition() {
    if (pcgfResultCount > 0) {
        // Position the suggestion list under the current line of the user list
        var listPosition = pcgfCurrentElement.offset();
        var currentLine = pcgfCurrentElement.val();
        currentLine = currentLine.substr(0, pcgfCurrentElement.prop('selectionStart')).split('\n').length;
        pcgfSuggestionList.css({
            display: 'inline-block',
            left: listPosition.left,
            top: listPosition.top + (currentLine * parseInt(pcgfCurrentElement.css('line-height'))) + 5
        });
    }
}

function setSuggestionName(name) {
    // Replace the current selected line with the new suggested name
    var currentPosition = pcgfCurrentElement.prop('selectionStart');
    var searchValue = pcgfCurrentElement.val();
    var startIndex = searchValue.lastIndexOf('\n', currentPosition - 1) + 1;
    var endIndex = searchValue.indexOf('\n', currentPosition);
    if (endIndex < 0) {
        endIndex = searchValue.length;
    }
    pcgfCurrentElement.val(searchValue.substr(0, startIndex) + name + '\n' + searchValue.substr(endIndex));
    pcgfCurrentElement.prop('selectionStart', startIndex + name.length + 1);
    pcgfCurrentElement.prop('selectionEnd', startIndex + name.length + 1);
    hideSuggestions();
    pcgfCurrentElement.focus();
    pcgfCurrentElement = null;
}

$(window).resize(function() {
    // Refresh the position of the suggestion list when the screen resizes
    setSuggestionPosition();
});

function nameSuggestionClick() {
    // Refresh the list when something has been clicked inside the textarea
    if (pcgfCurrentElement !== null) {
        pcgfCurrentElement.trigger('keyup');
    }
}

function nameSuggestionFocusIn() {
    // Show the suggestion list when the textarea get's focused
    if (pcgfCurrentElement !== null) {
        pcgfCurrentElement.trigger('keyup');
    }
}

function nameSuggestionFocusOut(e) {
    // Hide the suggestion list when nothing is selected
    if (pcgfLastSelectedIndex > 0) {
        e.preventDefault();
        e.stopPropagation();
        return;
    }
    hideSuggestions();
}

function nameSuggestionKeyDown(e) {
    if (pcgfSuggestionList.css('display') !== 'none') {
        pcgfKeyCatched++;
        if (e.which === 13) {
            // Enter selects the current name and replaces the current line of the textarea
            pcgfSuggestionList.trigger('click');
            pcgfKeyCatched = 13;
        } else if (e.which === 27) {
            // Escape closes the current suggestion list
            hideSuggestions();
        } else if (e.which === 38) {
            // Up arrow selects the previous entry of the list
            if (pcgfKeyCatched % 3 === 1) {
                if (pcgfLastSelectedIndex <= 1) {
                    pcgfLastSelectedIndex = pcgfResultCount;
                    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfResultCount + ')').addClass('selected');
                    if (pcgfResultCount > 1) {
                        pcgfSuggestionList.find('ul > li:nth-child(1)').removeClass('selected');
                    }
                } else {
                    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').removeClass('selected');
                    pcgfLastSelectedIndex--;
                    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').addClass('selected');
                }
            }
            if (pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').hasClass('title')) {
                pcgfCurrentElement.trigger(e);
            }
        } else if (e.which === 40) {
            // Down arrow selects the next entry of the list
            if (pcgfKeyCatched % 3 === 1) {
                if (pcgfLastSelectedIndex < 0 || pcgfLastSelectedIndex >= pcgfResultCount) {
                    pcgfLastSelectedIndex = 1;
                    pcgfSuggestionList.find('ul > li:nth-child(1)').addClass('selected');
                    if (pcgfResultCount > 1) {
                        pcgfSuggestionList.find('ul > li:nth-child(' + pcgfResultCount + ')').removeClass('selected');
                    }
                } else {
                    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').removeClass('selected');
                    pcgfLastSelectedIndex++;
                    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').addClass('selected');
                }
            }
            if (pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').hasClass('title')) {
                pcgfCurrentElement.trigger(e);
            }
        } else {
            pcgfKeyCatched = 0;
        }
        if (pcgfKeyCatched > 0) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
}

function nameSuggestionKeyUp(e) {
    if (pcgfKeyCatched > 0) {
        pcgfKeyCatched = 0;
        e.preventDefault();
        e.stopPropagation();
        return;
    }
    // Any other key not catched by keydown will lead to a refresh of the list
    pcgfCurrentElement = $(this);
    var currentPosition = pcgfCurrentElement.prop('selectionStart');
    if (currentPosition !== pcgfCurrentElement.prop('selectionEnd')) {
        // Don't show the suggestion list when multiple characters are selected
        hideSuggestions();
        return;
    }
    var searchValue = pcgfCurrentElement.val();
    var startIndex = searchValue.lastIndexOf('\n', currentPosition - 1) + 1;
    var endIndex = searchValue.indexOf('\n', currentPosition);
    if (endIndex < 0) {
        endIndex = searchValue.length;
    }
    hideSuggestions();
    searchValue = searchValue.substr(startIndex, endIndex - startIndex);
    if (searchValue !== pcgfLastSearchValue) {
        pcgfLastSearchValue = searchValue;
        $.ajax({
            url: pcgfNameSuggestionURL, type: 'POST', data: {search: searchValue, event: pcgfNameSuggestionEvent, selector: pcgfCurrentElement.attr('data-selector')}, success: function(result) {
                if (searchValue === pcgfLastSearchValue) {
                    var names = pcgfCurrentElement.val().split('\n');
                    var userSuggestions = '';
                    var groupSuggestions = '';
                    if (result.length === 0) {
                        // Make the suggestion list invisible if no match could be found
                        pcgfLastSearchValue = '';
                        return;
                    } else {
                        pcgfResultCount = 0;
                        var found;
                        if (result['users'] !== undefined) {
                            for (var i = 0; i < result['users'].length; i++) {
                                // If name isn't already entered then show it
                                found = false;
                                for (var j = 0; j < names.length; j++) {
                                    if (result['users'][i].username === names[j]) {
                                        found = true;
                                        break;
                                    }
                                }
                                if (!found) {
                                    pcgfResultCount++;
                                    userSuggestions += '<li>' + result['users'][i].avatar + result['users'][i].user + '<input type="hidden" class="pcgf-name-suggestion" value="' + result['users'][i].username + '"/></li>';
                                }
                            }
                        }
                        if (result['groups'] !== undefined) {
                            for (i = 0; i < result['groups'].length; i++) {
                                // If name isn't already entered then show it
                                found = false;
                                for (j = 0; j < names.length; j++) {
                                    if (result['groups'][i].groupname === names[j]) {
                                        found = true;
                                        break;
                                    }
                                }
                                if (!found) {
                                    pcgfResultCount++;
                                    groupSuggestions += '<li>' + result['groups'][i].avatar + result['groups'][i].group + '<input type="hidden" class="pcgf-name-suggestion" value="' + result['groups'][i].groupname + '"/></li>';
                                }
                            }
                        }
                    }
                    if (pcgfResultCount > 0) {
                        // Show the result list and refresh it's position
                        var suggestions = '';
                        if (result['groups'] !== undefined && userSuggestions !== '') {
                            suggestions += '<li class="title">' + pcgfNameSuggestionLanguageUsers + '</li>';
                            pcgfResultCount++;
                        }
                        suggestions += userSuggestions;
                        if (result['users'] !== undefined && groupSuggestions !== '') {
                            suggestions += '<li class="title">' + pcgfNameSuggestionLanguageGroups + '</li>';
                            pcgfResultCount++;
                        }
                        suggestions += groupSuggestions;
                        pcgfSuggestionList.html('<ul>' + suggestions + '</ul>');
                        pcgfSuggestionList.find('img').each(function() {
                            $(this).css({
                                width: pcgfNameSuggestionImageSize + 'px',
                                height: pcgfNameSuggestionImageSize + 'px'
                            });
                        });
                        setSuggestionPosition();
                    } else {
                        pcgfLastSearchValue = '';
                    }
                }
            }
        });
    } else if (pcgfLastSearchValue !== '' && pcgfSuggestionList.html() !== '') {
        setSuggestionPosition();
    }
}

pcgfSuggestionList.on('mousemove', 'ul > li', function() {
    // Select the list element where the cursor is above it
    if (pcgfLastSelectedIndex > 0) {
        pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').removeClass('selected');
    }
    pcgfLastSelectedIndex = $(this).index() + 1;
    pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').addClass('selected');

});

pcgfSuggestionList.on('mouseleave', function() {
    // Unselect the last selection of the suggestion
    if (pcgfLastSelectedIndex > 0) {
        pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').removeClass('selected');
    }
    pcgfLastSelectedIndex = -1;
});

pcgfSuggestionList.on('click', function() {
    // Select the name
    if (pcgfLastSelectedIndex > 0) {
        var suggestionName = pcgfSuggestionList.find('ul > li:nth-child(' + pcgfLastSelectedIndex + ')').find('.pcgf-name-suggestion').val();
        if (suggestionName !== undefined) {
            pcgfLastSearchValue = '';
            setSuggestionName(suggestionName);
        }
    }
});

$(document).ready(function() {
    var element;
    $.each(pcgfSelectors, function(index, value) {
        element = $(value);
        element.attr('data-selector', value);
        element.prop('autocomplete', 'off');
        element.on('click', nameSuggestionClick);
        element.on('focusin', nameSuggestionFocusIn);
        element.on('focusout', nameSuggestionFocusOut);
        element.on('keydown', nameSuggestionKeyDown);
        element.on('keyup', nameSuggestionKeyUp);
    });
});