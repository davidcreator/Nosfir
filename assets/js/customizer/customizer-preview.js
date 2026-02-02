/**
 * Nosfir Customizer Preview
 *
 * @package Nosfir
 * @since 1.0.0
 */

(function($, api) {
    'use strict';

    if (typeof api === 'undefined') {
        console.log('Nosfir: Customizer Preview API not available');
        return;
    }

    // Site Title
    api('blogname', function(setting) {
        setting.bind(function(value) {
            $('.site-title a').text(value);
        });
    });

    // Site Description
    api('blogdescription', function(setting) {
        setting.bind(function(value) {
            $('.site-description').text(value);
        });
    });

    // Primary Color
    api('nosfir_primary_color', function(setting) {
        setting.bind(function(value) {
            document.documentElement.style.setProperty('--nosfir-primary', value);
        });
    });

    // Hero Title
    api('nosfir_hero_title', function(setting) {
        setting.bind(function(value) {
            $('.nosfir-hero__title').html(value);
        });
    });

    // Hero Subtitle
    api('nosfir_hero_subtitle', function(setting) {
        setting.bind(function(value) {
            $('.nosfir-hero__subtitle').html(value);
        });
    });

    // Footer Copyright
    api('nosfir_footer_copyright', function(setting) {
        setting.bind(function(value) {
            $('.footer-credits .copyright').html(value);
        });
    });

})(jQuery, wp.customize);