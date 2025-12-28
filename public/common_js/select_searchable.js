var clear_setTimeout;

const rawOptionsAll = {};

function handleKeyPressForSuggestion(keyPress) {
  // Key codes for different categories
  const keyFunctions = new Set([27, 13, 9, 38, 40]); // ESC, ENTER, TAB, up arrow, down arrow
  const validSuggestionKeys = new Set([
    8,
    32,
    46,
    106,
    107,
    109,
    110,
    111,
    187,
    188,
    189,
    219,
    221,
    229,
    173, // Backspace, space, delete, operators, brackets, dash
    ...Array.from({ length: 10 }, (_, i) => i + 48), // 0-9
    ...Array.from({ length: 10 }, (_, i) => i + 96), // numpad 0-9
    ...Array.from({ length: 26 }, (_, i) => i + 65), // A-Z
  ]);
  const nonFunctionalKeys = new Set([
    16,
    17,
    18,
    19,
    20,
    33,
    34,
    35,
    36,
    45,
    91,
    92,
    93,
    144,
    145,
    186,
    190,
    191,
    220,
    192,
    222,
    ...Array.from({ length: 12 }, (_, i) => i + 112), // F1-F12
    37,
    39, // Left arrow, right arrow
  ]);

  // Check if key is part of 'key_function' group
  if (keyFunctions.has(keyPress)) {
    return 'key_function';
  }
  // Check if key is a valid key for making suggestions
  else if (validSuggestionKeys.has(keyPress)) {
    return 'make_suggestion';
  }
  // Check if key is part of 'non_functional' group (should not trigger any action)
  else if (nonFunctionalKeys.has(keyPress)) {
    return false;
  }
  // If key doesn't belong to any defined group, show a message and return false
  else {
    const msg =
      `You pressed keycode (${keyPress}) for searching.\n` +
      'But this keycode is not defined yet.\n' +
      'Please tell us to solve this issue.\n\n' +
      'Thank you for your patience.';
    alert(msg);
    return false;
  }
}

function auto_suggestion_div__action(keyPress, element, which_to_affect, _function_) {
  if (keyPress == 27) {
    // ESC
    auto_suggestion_div__action_cancel(element, which_to_affect);
    return false;
  } else if (keyPress == 13 || keyPress == 9) {
    // ENTER / TAB
    var search_value = $(`#${element}_make_searchable`).val();
    var choose_option = $('#auto_suggestion_div div.selected').length;

    if (search_value == '') {
      auto_suggestion_div__action_cancel(element, which_to_affect);
    } else {
      if (choose_option == 1) {
        var selected_row_value = $('#auto_suggestion_div div.selected').html();
        selected_row_value = $('<div/>').html(selected_row_value).text();

        if (search_value == selected_row_value) {
          var affect_value = $('#auto_suggestion_div div.selected').attr('data-value');
          auto_suggestion_div__action_accept(element, which_to_affect, affect_value, _function_);
        } else {
          auto_suggestion_div__action_cancel(element, which_to_affect);
        }
      } else {
        var affect_value = auto_suggestion_div__action_search_best_match(element);
        if (affect_value) {
          auto_suggestion_div__action_accept(element, which_to_affect, affect_value, _function_);
        } else {
          // Best Matching Search
          // First Matching Search
          auto_suggestion_div__action_cancel(element, which_to_affect);
        }
      }
    }
    return false;
  }
  // up arrow / down arrow / left arrow / right arrow
  else if (keyPress == 38 || keyPress == 40 || keyPress == 37 || keyPress == 39) {
    var suggestion_no = $('#auto_suggestion_div div').length;
    suggestion_no--;
    var _index = $('#auto_suggestion_div div.selected').index();

    // if( _index<0 ){ _index = 0; }

    if (keyPress == 38) {
      // keyUp
      if (_index <= 0) {
        _index = suggestion_no;
      } else {
        _index--;
      }
    } // keyDown
    else {
      if (_index >= suggestion_no) {
        _index = 0;
      } else {
        _index++;
      }
    }
    // $('#alias').val( suggestion_no+' > '+_index );

    // loop through each result div applying the correct style
    $('#auto_suggestion_div')
      .children()
      .each(function (i) {
        if (i == _index) {
          $(`#${element}_make_searchable`).val(this.childNodes[0].nodeValue);
          this.className = 'selected';
        } else {
          this.className = 'unselected';
        }
      });

    return false;
  }
}
function auto_suggestion_div__action_cancel(element, which_to_affect) {
  $('#auto_suggestion_div').remove();

  if (which_to_affect == '') {
    // element it is a <select>
    $(`#${element}_make_searchable`).val($(`#${element}`).find('option:selected').text());
  } else {
    // it is a <input>
  }
}
function auto_suggestion_div__action_accept(element, which_to_affect, affect_value, _function_) {
  $('#auto_suggestion_div').remove();

  if (which_to_affect == '') {
    // element it is a <select>
    $(`#${element}`).val(affect_value);
    $(`#${element}_make_searchable`).val($(`#${element}`).find('option:selected').text());
  } else {
    // it is a <input>
  }

  if (typeof _function_ === 'function') {
    _function_();
  }
}
function auto_suggestion_div__action_search_best_match(element) {
  var search_value = $(`#${element}_make_searchable`).val();
  search_value = search_value.toLowerCase();
  search_value = search_value.trim();
  // alert( 'search_value : '+search_value );
  var matches = 0;
  var affect_value = '';

  $('#auto_suggestion_div div').each(function () {
    var suggestion = $(this).text();
    suggestion = $('<div/>').html(suggestion).text();
    suggestion = suggestion.toLowerCase();
    suggestion = suggestion.trim();
    // alert( 'suggestion : '+suggestion );

    if (suggestion == search_value) {
      affect_value = $(this).attr('data-value');
      return;
    } else if (suggestion.indexOf(search_value) != -1) {
      matches++;
      affect_value = $(this).attr('data-value');
    }
  });

  if (matches <= 1) {
    return affect_value;
  } else {
    return false;
  }
  // alert( 'matches:'+matches+', '+affect_value );
}

function make_select_searchable(element, e, _function_) {
  let keyPress = e.which;
  let make_decision = handleKeyPressForSuggestion(keyPress);

  if (make_decision == 'key_function') {
    auto_suggestion_div__action(keyPress, element, '', _function_);
    return false;
  } else if (make_decision == 'make_suggestion') {
    if (clear_setTimeout) {
      clearTimeout(clear_setTimeout);
    }

    clear_setTimeout = setTimeout(function () {
      let value = $(`#${element}_make_searchable`).val();
      let new_options = new Array();
      let duplicateValue = {};

      if (value.trim() == '') {
        return rawOptionsAll[`${element}_option`];
      }
      rawOptionsAll[`${element}_option`].forEach((item) => {
        if (new_options.length > 20) {
          return; // Exit the loop early
        }
        if (item[1].toLowerCase().startsWith(value?.trim().toLowerCase())) {
          new_options.push(item);
          duplicateValue[item[0]] = item;
        } // Simulate LIKE search
      });
      rawOptionsAll[`${element}_option`].forEach((item) => {
        if (new_options.length > 20) {
          return; // Exit the loop early
        }
        if (
          item[1].toLowerCase().includes(value?.trim().toLowerCase()) &&
          !duplicateValue.hasOwnProperty(item[0])
        ) {
          new_options.push(item);
          duplicateValue[item[0]] = item;
        } // Simulate LIKE search
      });
      let search = value.split(' ');
      for (var i = 0; i < search.length; i++) {
        var this_value = search[i];
        var this_value = this_value.trim();

        if (this_value != '') {
          let value = new RegExp(this_value, 'i');
          rawOptionsAll[`${element}_option`].forEach((x) => {
            if (new_options.length > 20) {
              return; // Exit the loop early
            }
            if (x[1].search(value) != -1) {
              if (!duplicateValue.hasOwnProperty(x[0])) {
                new_options.push(x);
                duplicateValue[x[0]] = x;
              }
            }
          });
        }
        if (new_options.length > 20) {
          break; // Exit the outer for loop if the condition is met
        }
      }

      // new_options.sort((a, b) => parseInt(a[2], 10) - parseInt(b[2], 10));
      // new_options.sort((a, b) => parseInt(b[3], 10) - parseInt(a[3], 10));

      if (new_options.length) {
        if (!$('#auto_suggestion_div').length) {
          var div_height = $(`#${element}_make_searchable`).height();
          var div_width = $(`#${element}_make_searchable`).width() + 8;
          var div_top = $(`#${element}_make_searchable`).offset().top + div_height + 10;
          var div_left = $(`#${element}_make_searchable`).offset().left;

          $('body').append('<div id="auto_suggestion_div"></div>');
          // apply the css styles - optimized for Firefox
          $('#auto_suggestion_div').css('position', 'absolute');
          $('#auto_suggestion_div').offset({
            top: div_top,
            left: div_left,
          });
          $('#auto_suggestion_div').width(div_width);
        }

        let newData = new_options
          .map((x) => `<div class="unselected" data-value="${x[0]}">${x[1]}</div>`)
          .join('');
        $('#auto_suggestion_div').fadeOut(100, function () {
          $(this).html(newData).fadeIn(100);
        });
        let which_to_affect = '';
        $(`#${element}_make_searchable`).blur(function () {
          setTimeout(function () {
            auto_suggestion_div__action_cancel(element, which_to_affect);
            return false;
          }, 200);
        });

        // on mouse over clean previous selected and set a new one
        $('#auto_suggestion_div').on('mouseover', ' > div', function () {
          $('#auto_suggestion_div > div').each(function () {
            this.className = 'unselected';
          });
          $(this).attr('class', 'selected');
          // result_current = $(this).index()+1;
        });

        // on click copy the result text to the search field and hide
        $('#auto_suggestion_div').on('click', ' > div', function () {
          var _value1 = this.childNodes[0].nodeValue;
          var _value2 = $(this).attr('data-value');

          $(`#${element}_make_searchable`).val(_value1);
          $(`#${element}_make_searchable`).focus();
          $('#auto_suggestion_div').remove();

          if (which_to_affect == '') {
            // element it is a <select>
            $(`#${element}`).val(_value2);
          } else {
            // it is a <input>
          }
        });
      } else {
        $('#auto_suggestion_div').remove();
      }
    }, 10);
  }
}

$(document).ready(function () {
  $(window).resize(function () {
    $("input[id$='_make_searchable']").each(function (i, obj) {
      var _searchable1 = $(this).attr('id');
      var _searchable2 = _searchable1.replace('_make_searchable', '');

      _searchable2 = $('#' + _searchable2);

      $('#' + _searchable1).offset({
        top: _searchable2.offset().top + 0,
        left: _searchable2.offset().left + 0,
      });
      $('#' + _searchable1).height(_searchable2.height() + 2);
      $('#' + _searchable1).width(_searchable2.width() - 50);
    });
  });
});

(function ($) {
  $.fn.make_searchable = function (_function_) {
    var parameter_fun = "''";
    if (typeof _function_ === 'function') {
      var parameter_fun = _function_.name;
    }

    if (this.length == 0) {
      alert('You tried to make searchable is not found');
      return false;
    } else {
      // let name = this.attr("name");
      let name = this.attr('id');
      let s_name = name + '_make_searchable';
      let _value = $(this).find('option:selected').text()?.trim();
      rawOptionsAll[`${name}_option`] = [];
      $(`#${name} option`).each(function (i) {
        var option_value = $(this).attr('value');
        if (option_value != '') {
          let options = new Array();
          options[0] = option_value;
          options[1] = $(this).text();
          options[2] = i;
          options[3] = '1'; // found times
          rawOptionsAll[`${name}_option`].push(options);
        }
      });

      var _onkeydown = `make_select_searchable('${name}',event,${parameter_fun})`;

      var to_append = `<input 
								type="text" 
								name="${s_name}" 
								id="${s_name}"
								placeholder="type here to search"
								autocomplete="off"
								class="w3-input no_print"
								value="${_value}"
                                onkeyup="${_onkeydown}"
                                />`;
      // onkeydown="${_onkeydown}"

      this.before(to_append);
      // $('body').append(to_append);
      // apply the css styles - optimized for Firefox
      $('#' + s_name).css('position', 'absolute');
      $('#' + s_name).css('background-color', 'white');
      $('#' + s_name).css('opacity', '1');
      $('#' + s_name).offset({
        top: $(this).offset().top + 0,
        left: $(this).offset().left + 0,
      });
      $('#' + s_name).height($(this).height() + 2);
      $('#' + s_name).width($(this).width() - 50);

      $('#' + s_name).focus(function () {
        $(this).css('opacity', '1');
        $(this).css('background-color', 'white');
        $(this).select();
      });
      $('#' + s_name).blur(function () {
        $(this).css('opacity', '1');
        $(this).css('background-color', 'white');
      });

      $(this).on('change', function () {
        var _value = $(this).find('option:selected').text();
        _value = _value.trim();

        $('#' + s_name).val(_value);
      });
    }
  };
})(jQuery);

(function ($) {
  $.fn._select_searchable_accounts_ledger = function (range) {
    //alert(aa);

    if ($('#element_id').length) {
    } else {
      $('body').append('<input type="hidden" name="element_id" id="element_id" />');
    }

    var _reserve_1 = 'copy_1_ledger_name___ex_1';
    var _reserve_2 = 'copy_1_ledger_hed_id_ex_1';
    var _reserve_3 = 'copy_1_ledger_range__ex_1';

    $('body').append(
      '<input type="hidden" id="' +
        _reserve_2 +
        '" name="' +
        _reserve_2 +
        '" readonly="" value="" />'
    );
    $('body').append(
      '<input type="hidden" id="' +
        _reserve_3 +
        '" name="' +
        _reserve_3 +
        '" readonly="" value="' +
        range +
        '" />'
    );
    $('body').append(
      '<input type="text" id="' +
        _reserve_1 +
        '" name="' +
        _reserve_1 +
        '" class="_select_searchable_css no_print" data-s_s="' +
        $(this).attr('id') +
        '" onBlur="" autocomplete="off" value="' +
        $(this).find('option:selected').text() +
        '" onkeydown="v2_down(event,\'' +
        _reserve_1 +
        "','1');\" />"
    );

    // apply the css styles - optimized for Firefox
    $('#' + _reserve_1).css('position', 'absolute');
    $('#' + _reserve_1).offset({
      top: $(this).offset().top + 2,
      left: $(this).offset().left + 2,
    });
    $('#' + _reserve_1).height($(this).height() + 4);
    $('#' + _reserve_1).width($(this).width() - 50);

    $(this).on('change', function () {
      $('#' + _reserve_1).val($(this).find('option:selected').text());
    });

    $('#' + _reserve_1).on('click', function () {
      $('#' + _reserve_1).select();
    });
  };
})(jQuery);

function _select_searchable_configure(id) {
  var _id = id.replace('ledger_name___ex_', 'ledger_hed_id_ex_');
  var _id = $('#' + _id).val();
  var s_s = $('#' + id).attr('data-s_s');

  if (_id != '') {
    if ($('#' + s_s + ' option[value="' + _id + '"]').length > 0) {
      $('#' + s_s).val(_id);
    }
  }

  $('#' + id).val(
    $('#' + s_s)
      .find('option:selected')
      .text()
  );
}
