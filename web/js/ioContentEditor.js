(function($) {

$.widget('ui.ioContentEditor', {
  
  options: {
    slotType:   null
  },
  
  _create: function() {
    this.initialize();
  },
  
  initialize: function() {
    var self = this;
    var form = this.getForm();
    
    // register the ajax form submit event
    this._ajaxForm();
    
    // hook up the cancel button
    $('input.cancel', this.getForm()).click(function() {
      // trigger a close event

      self.element.trigger('close');
      return false;
    });
    
    // hook up the done button
    // @todo have this handle form validation (don't just close)
    $('input.done', form).click(function() {
      // trigger a save + close event

      // tell myself to close on successful form submit
      self._setOption('close_on_form_success', true);
      form.submit();

      return false;
    });
    
    // register the ajax response event
    this._bindAjaxResponseEvent();
    
    this.getForm().trigger('ajaxResponseSuccess');
  },
  
  _ajaxForm: function() {
    // Attach the form ajax submit event to the form
    
    var self = this;
    var form = this.getForm();
    
    form.submit(function() {
      self.block();
      
      // trigger the event, allow anybody to prep anything
      $(this).trigger('ioPreFormSubmit');

      $(this).ajaxSubmit({
        dataType: 'json',
        error: function(xhr, textStatus, errorThrown) {
          self.unblock();
          // display some sort of error - this is better than nothing
          alert('There was a server error when your form was submitted. Your changes may not have been saved.');
        },
        success: function(responseText, statusText, xhr) {
          var isError = responseText.error != '';

          if (isError) {
            // display some sort of error
            //alert(result.error);
          }

          $('.form_body', form).html(responseText.response);
          form.trigger('ajaxResponseSuccess');
          form.trigger('formPostResponse', responseText);
          self.unblock();

          if (self.option('close_on_form_success') && !isError)
          {
            self._setOption('close_on_form_success', false);
            self.element.trigger('close');
          }
        }
      });
      
      return false;
    });
  },
  
  _bindAjaxResponseEvent: function() {
    // Creates an ajaxResponseSuccess, which should be triggered whenever
    // the contents of the form (.form_body) are ajaxed
    
    var self = this;
    var form = this.getForm();
    
    // register the ajaxSuccess function on this editor
    form.bind('ajaxResponseSuccess', function() {
      // do something once the form is loaded
    });
  },
  
  block: function() {
    // don't do anything if blockUI isn't available
    if (!$.blockUI)
    {
      return;
    }
    // If we're not working on a block element, we've gotta block the whole page
    if (this.isBlock())
    {
      // you actually want to block the parent, (i.e. #facebox-wrapper)
      this.element.parent().block();
    }
    else
    {
      $.blockUI();
    }
  },
  
  unblock: function() {
    // don't do anything if blockUI isn't available
    if (!$.blockUI)
    {
      return;
    }

    if (this.isBlock())
    {
      this.element.parent().unblock();
    }
    else
    {
      $.unblockUI();
    }
  },
  
  isBlock: function() {
    return (this.element.css('display') == 'block');
  },
  
  getForm: function() {
    if (!this.option('form'))
    {
      this._setOption('form', $('form', this.element));
    }
    
    return this.option('form');
  },
  
  destroy: function() {
    // unbind all the close events
    this.getForm().unbind('close');
    
    // destroy this widget
    $.Widget.prototype.destroy.apply(this, arguments);
  }
});


})(jQuery);