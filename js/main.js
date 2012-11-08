window.onload = function() {
  (function() {
    var o = {
      init: function() {
        this.form = document.filters;
        this.brightnessField = this.form.brightness;
        this.contrastField = this.form.contrast;

        this.brightnessSlider = document.getElementById('brightness');
        this.contrastSlider = document.getElementById('contrast');

        this.brightnessCounter = document.getElementById('brightness-counter');
        this.contrastCounter = document.getElementById('contrast-counter');

        Drag.init(this.brightnessSlider, null, 0, 200, 0, 0);
        Drag.init(this.contrastSlider, null, 0, 200, 0, 0);

        return this;
      },

      events: function() {
        var self = this;

        this.brightnessSlider.onDrag = function(x, y) {
          self.setCounter(x, 'brightness');
        };

        this.contrastSlider.onDrag = function(x, y) {
          self.setCounter(x, 'contrast');
        };

        this.form.submit = function(e) {
          e = e || event;

          e.preventDefault ? e.preventDefault() : e.returnValue = false;
        };

        return this;
      },

      setCounter: function(x, filter) {
        x -= 100;
        if (filter == 'brightness') {
          this.brightnessCounter.innerHTML = x;
          this.brightnessField.value = x;
        }

        if (filter == 'contrast') {
          this.contrastCounter.innerHTML = x;
          this.contrastField.value = x;
        }
      }
    };

    o.init().events();

  })();
};