// Main JS file for Gideon's Technology website
(function($) {
    'use strict';
    
    $(function() {
        // Preloader
        $(window).on('load', function() {
            $('#preloader').fadeOut(1000);
        });

        // Fix for the width function error
        // Using proper jQuery methods instead of direct width access
        $('.element-with-width').each(function() {
            var width = $(this).outerWidth();
            // Do something with width
        });

        // Navigation toggle
        $('.navbar-toggler').on('click', function() {
            $('.navbar-collapse').toggleClass('show');
        });

        // Submenu toggle for mobile
        if ($(window).width() < 768) {
            $('.menu-item-has-children > a').on('click', function (e) {
                e.preventDefault();
                $(this).siblings('.sub-menu').slideToggle();
            });
        }

        // Back to top button
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 200) {
                $('.back-to-top').addClass('active');
            } else {
                $('.back-to-top').removeClass('active');
            }
        });

        $('.back-to-top').on('click', function () {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
            return false;
        });

        // Initialize sliders if jQuery slick exists
        if ($.fn.slick && $('.services-slider').length) {
            $('.services-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: false,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                        }
                    }
                ]
            });
        }

        if ($.fn.slick && $('.category-slider').length) {
            $('.category-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: false,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                        }
                    }
                ]
            });
        }

        // Initialize WOW.js for animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Notification dropdown toggle
        $('.notification-icon').on('click', function (e) {
            e.preventDefault();
            $('.notification-list-wrapper').toggleClass('show');
        });

        // Close notification dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.notification-icon').length && !$(e.target).closest('.notification-list-wrapper').length) {
                $('.notification-list-wrapper').removeClass('show');
            }
        });

        // Custom select initialization
        if ($('select').length) {
            $('select').niceSelect();
        }

        // Smooth scroll for anchor links
        $('a.scroll-link').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('html, body').animate({
                scrollTop: $(target).offset().top - 70
            }, 1000);
        });

        // Form validation
        $('form').on('submit', function(e) {
            // Basic form validation
            var valid = true;
            $(this).find('input.required, textarea.required').each(function() {
                if ($(this).val().trim() === '') {
                    valid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                return false;
            }
        });

        // Responsive behavior
        $(window).on('resize', function() {
            // Responsive adjustments
        });
    });
})(jQuery);