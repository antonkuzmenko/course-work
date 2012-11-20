window.onload = function () {
  (function () {
    var o = {
      init: function () {
        this.form = document.filters;
        this.brightnessField = this.form.brightness;
        this.contrastField = this.form.contrast;

        this.brightnessSlider = document.getElementById('brightness');
        this.contrastSlider = document.getElementById('contrast');
        this.preview = document.getElementById('preview');

        this.brightnessCounter = document.getElementById('brightness-counter');
        this.contrastCounter = document.getElementById('contrast-counter');

        Drag.init(this.brightnessSlider, null, 0, 200, 0, 0);
        Drag.init(this.contrastSlider, null, 0, 200, 0, 0);

        return this;
      },

      events: function () {
        var self = this;

        this.brightnessSlider.onDrag = function (x, y) {
          self.action.setCounter.call(self, x, 'brightness');
        };

        this.contrastSlider.onDrag = function (x, y) {
          self.action.setCounter.call(self, x, 'contrast');
        };

        this.form.onsubmit = function (e) {
          e = e || event;

          e.preventDefault ? e.preventDefault() : e.returnValue = false;
        };

        this.form['preview-button'].onclick = function(e) {
          self.action.setPreview.call(self);
        };

        this.form['field-submit'].onclick = function(e) {
          self.form.submit();
        };

        return this;
      },

      helper: {
        remove: function(elem) {
          elem.parentNode.removeChild(elem);
        }
      },

      action: {
        /**
         * Set brightness or contrast.
         *
         * @param x
         *
         * @param filter
         *  brightness|contrast
         */
        setCounter: function (x, filter) {
          x -= 100;
          if (filter == 'brightness') {
            this.brightnessCounter.innerHTML = x;
            this.brightnessField.value = x;
          }

          if (filter == 'contrast') {
            this.contrastCounter.innerHTML = x;
            this.contrastField.value = x;
          }
        },

        setPreview: function () {
          var self = this;

          var elements = self.form.elements;
          var i = elements.length;

          var settings = {};
          while (i--) {
            if (elements[i].name) {
              var value = 0;
              var key = elements[i].name;

              switch (elements[i].type) {
                case 'checkbox':
                  if (elements[i].checked) {
                    value = 1;
                  }
                  break;

                default:
                  value = elements[i].value;
                  break;
              }

              settings[key] = value;
            }
          }

          self.action.changeImage.call(self, settings);
        },

        changeImage: function(settings) {
          reqwest({
            url: 'ajax.php',
            method: 'get',
            type: 'text/html',
            data: settings,
            error: function (err) {
              alert('Пожалуйста, сообщите разработчику об ошибке');
            },
            success: function (data) {
              var image = document.createElement('IMG');
              image.src = data.response;
              image.setAttribute('width', 300);
              self.preview.innerHTML = '';
              self.preview.appendChild(image);
            }
          });
        }
      }
    };

    o.init().events();

  })();
};