import $ from 'jquery';
import BaseView from 'oroui/js/app/views/base/view';
import tools from 'oroui/js/tools';

require('slick');

const NorsysBannerView = BaseView.extend({
    autoplaySpeed: 4000,
    speed: 1500,

    constructor: function NorsysBannerView(options) {
        NorsysBannerView.__super__.constructor.call(this, options);
    },

    initialize: function(options) {
        NorsysBannerView.__super__.initialize.call(this, options);

        this.$el = options._sourceElement;
        this.isSlider = options.isSlider;
        this.$isMobile = tools.isMobile();

        this.$bannerContainer = $('[data-banner-container]');
        this.$bannerCloseButton = $('[data-banner-close]');
        this.$pageMainContent = $('.page-main__content');
        this.$productText = $('#product-description');
        this.$productTable = $('#technical-table');

        this._renderBanner();
        this.updatePageContentStyle();
    },

    _renderBanner: function() {
        if (this.isSlider) {
            this._initBannerSlider();
        }

        this._onCloseClick();
    },

    _onCloseClick: function() {
        const self = this;

        this.$bannerCloseButton.on('click.' + this.cid, function() {
            self._closeBanner();
            self.$productText.add(self.$productTable).removeClass('has-banner-offset');
        });
    },

    _closeBanner: function() {
        this.$el.remove();
        this.createClosedCookie();
        this.updatePageContentStyle();
    },

    _initBannerSlider: function() {
        this.$bannerContainer.slick({
            slidesToShow: 1,
            arrows: false,
            dots: false,
            autoplay: true,
            autoplaySpeed: this.autoplaySpeed,
            speed: this.speed,
            vertical: true,
            verticalScrolling: true,
            pauseOnHover: true,
            pauseOnFocus: true,
            draggable: false,
            infinite: true,
            adaptiveHeight: true
        });
    },

    updatePageContentStyle: function() {
        const $headerHeight = $('.page-header').outerHeight();
        const $menuHeight = this.$isMobile ? 0 : $('[data-header-menu]').outerHeight() || 0;
        const $marginWithBanner = $headerHeight + $menuHeight;

        this.$pageMainContent.css('margin-top', `${$marginWithBanner}px`);
        this.$productText.add(this.$productTable).addClass('has-banner-offset');
    },

    createClosedCookie: function() {
        const now = new Date();

        document.cookie = 'closed_banner_date=' + now.getTime() + '; expires=' + this.formatExpireDate(now);
    },

    formatExpireDate: function(now) {
        return new Date(new Date(now).setDate(now.getDate() + 60)).toUTCString();
    }
});

export default NorsysBannerView;
