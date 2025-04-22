define(function (require) {
    'use strict';

    const BaseView = require('oroui/js/app/views/base/view');

    const NorsysBannerView = BaseView.extend({
        initialize: function (options) {
            NorsysBannerView.__super__.initialize.call(this, options);
            this.$banner = options._sourceElement;
            this.isSticky = options.isSticky;
            this.oroMobilePanel = $('.sticky-panel--top');

            this._activateSticky();
        },

        _activateSticky: function () {
            if (this.isSticky) {
                this._onWindowScroll();
                this._onWindowLoad();
            }
        },

        _onWindowScroll: function () {
            const self = this;
            const originalBannerPositionTop = this.$banner.offset().top;

            $(window).on('scroll.' + this.cid, function () {
                if ($(window).scrollTop() > originalBannerPositionTop) {
                    self._setSticky();
                }

                if ($(window).scrollTop() < originalBannerPositionTop) {
                    self._unsetSticky();
                }
            });
        },

        _onWindowLoad: function () {
            const self = this;
            $(window).on('load', function () {
                self._setSticky();
            });
        },

        _setSticky: function () {
            this.$banner.addClass('sticky');

            if (this.oroMobilePanel.hasClass('has-content')) {
                this.$banner.css('top', this.oroMobilePanel.height());
            } else {
                this.$banner.css('top', '0');
            }

            this.$banner.next('.page-area-container').css('margin-top', this.$banner.height());
        },

        _unsetSticky: function () {
            this.$banner.removeClass('sticky');
            this.$banner.next('.page-area-container').css('margin-top', '0');
        }
    });

    return NorsysBannerView;
});