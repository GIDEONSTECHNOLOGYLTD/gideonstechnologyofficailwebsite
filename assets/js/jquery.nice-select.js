/**
 * jQuery Nice Select - v1.0
 * Simple select replacement
 */

(function($) {

  $.fn.niceSelect = function(method) {
    
    // Methods
    if (typeof method == 'string') {      
      if (method == 'update') {
        this.each(function() {
          var $select = $(this);
          var $dropdown = $(this).next('.nice-select');
          
          $dropdown.remove();
          $select.removeData('nice-select');
          $select.removeClass('nice-select-active');
          $select.niceSelect();
        });
      } else if (method == 'destroy') {
        this.each(function() {
          var $select = $(this);
          var $dropdown = $(this).next('.nice-select');
          
          if ($dropdown.length) {
            $dropdown.remove();
            $select.removeData('nice-select');
            $select.removeClass('nice-select-active');
          }
        });
        if ($.fn.niceSelect.listeners) {
          $(document).off('click.nice_select');
          $.fn.niceSelect.listeners = 0;
        }
      } else {
        console.log('Method "' + method + '" does not exist.');
      }
      return this;
    }
      
    // Create custom markup
    this.each(function() {
      var $select = $(this);
      
      if (!$select.data('nice-select')) {
        // Hide native select
        $select.addClass('nice-select-active');
        
        // Create custom markup
        var $dropdown = $('<div class="nice-select"></div>');
        var $current = $('<span class="current"></span>');
        var $list = $('<ul class="list"></ul>');
        
        $select.after($dropdown);
        
        $dropdown.addClass($select.attr('class') || '');
        $dropdown.append($current);
        $dropdown.append($list);
        
        var $options = $select.find('option');
        var $selected = $select.find('option:selected');
        
        $current.text($selected.text() || $selected.data('display') || '');
        
        $options.each(function() {
          var $option = $(this);
          var display = $option.data('display') || '';
          
          $list.append($('<li class="option ' + 
            ($option.is(':selected') ? 'selected' : '') + 
            ($option.is(':disabled') ? 'disabled' : '') +
            '" data-value="' + $option.val() + '" data-display="' + display + '">' + 
            $option.text() + '</li>'));
        });
        
        $select.data('nice-select', $dropdown);
      }
    });
    
    // Event listeners
    if ($.fn.niceSelect.listeners != 1) {
      $(document).on('click.nice_select', '.nice-select', function(event) {
        var $dropdown = $(this);
        
        $('.nice-select').not($dropdown).removeClass('open');
        $dropdown.toggleClass('open');
        
        if ($dropdown.hasClass('open')) {
          $dropdown.find('.option');
          $dropdown.find('.focus').removeClass('focus');
          $dropdown.find('.selected').addClass('focus');
        } else {
          $dropdown.focus();
        }
      });
      
      $(document).on('click.nice_select', function(event) {
        if ($(event.target).closest('.nice-select').length === 0) {
          $('.nice-select').removeClass('open').find('.option');
        }
      });
      
      $(document).on('click.nice_select', '.nice-select .option:not(.disabled)', function(event) {
        var $option = $(this);
        var $dropdown = $option.closest('.nice-select');
        
        $dropdown.find('.selected').removeClass('selected');
        $option.addClass('selected');
        
        var text = $option.data('display') || $option.text();
        $dropdown.find('.current').text(text);
        
        var $select = $dropdown.prev('select');
        $select.val($option.data('value')).trigger('change');
      });
      
      $.fn.niceSelect.listeners = 1;
    }
  };
  
}(jQuery));