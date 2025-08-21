(function($) {

    "use strict";

    jQuery(document).ready(function() {

        /*
         * -----------------------------------------------------------------
         *---------------------------Preloader------------------------------
         * -----------------------------------------------------------------
         */

        var fundlyWindow = $(window);
        var pagebody = $('html, body');
       

        fundlyWindow.on("load", function() {

            var preloader = jQuery('.preloader');
            var preloaderArea = jQuery('.preloader-area');
            preloader.fadeOut();
            preloaderArea.delay(200).fadeOut('slow');
            fundlyWindow.scrollTop(0);
        });



        /*
         * -----------------------------------------------------------------
         *-----------------------Scroll Top Events--------------------------
         * -----------------------------------------------------------------
         */



        var scrollTopBtn = $("#scroll-top-area");

        scrollTopBtn.on("click", function(e) {
            e.preventDefault();
            pagebody.animate({
                scrollTop: 0
            }, 2000);
        });

        fundlyWindow.on("scroll", function(e) {
            e.preventDefault();
            var top = fundlyWindow.scrollTop();
            var scrollTopArea = $("#scroll-top-area");
            if (top < 150) {
                scrollTopArea.css('display', 'none');                
                  
               // bg-primary.css('background', 'transparent');
            } else if (top >= 150) {
                scrollTopArea.css('display', 'block');
              //  bg-primary.css('background', '#303030');
            }
        });


        /*
         * -----------------------------------------------------------------
         *--------------------Animation using animate.css-------------------
         * -----------------------------------------------------------------
         */



        var animation1 = jQuery('.animation');

        animation1.waypoint(function() {
            var thisElement = $(this.element);
            var animation = thisElement.attr('data-animation');

            thisElement.css('opacity', '1');
            thisElement.addClass("animated " + animation).delay(2000);
        }, {
            offset: '75%',
        });

        /*
         * -----------------------------------------------------------------
         *------------------------------slicknav----------------------------
         * -----------------------------------------------------------------
         */

       

        //var menu = $("#menu");

        //menu.slicknav({
        //  label: '',
        //  duration: 1000,
        //  easingOpen: "easeOutBounce", //available with jQuery UI
        // });


    });

})(jQuery);






/* Tree()
 * ======
 * Converts a nested list into a multilevel
 * tree view menu.
 *
 * @Usage: $('.my-menu').tree(options)
 *         or add [data-widget="tree"] to the ul element
 *         Pass any option as data-option="value"
 */
    +function ($) {
        'use strict'

        var DataKey = 'lte.tree'

        var Default = {
            animationSpeed: 500,
                accordion     : true,
                followLink    : false,
                trigger       : '.treeview a'
  }

    var Selector = {
        tree        : '.tree',
        treeview    : '.treeview',
        treeviewMenu: '.treeview-menu',
        open        : '.menu-open, .active',
        li          : 'li',
        data        : '[data-widget="tree"]',
        active      : '.active'
    }

    var ClassName = {
        open: 'menu-open',
        tree: 'tree'
    }

    var Event = {
        collapsed: 'collapsed.tree',
        expanded : 'expanded.tree'
    }

  // Tree Class Definition
  // =====================
    var Tree = function (element, options) {
        this.element = element
        this.options = options

        $(this.element).addClass(ClassName.tree)

        $(Selector.treeview + Selector.active, this.element).addClass(ClassName.open)

        this._setUpListeners()
    }

    Tree.prototype.toggle = function (link, event) {
        var treeviewMenu = link.next(Selector.treeviewMenu)
        var parentLi     = link.parent()
        var isOpen       = parentLi.hasClass(ClassName.open)

        if (!parentLi.is(Selector.treeview)) {
            return
        }

        if (!this.options.followLink || link.attr('href') == '#') {
            event.preventDefault()
        }

        if (isOpen) {
            this.collapse(treeviewMenu, parentLi)
        } else {
            this.expand(treeviewMenu, parentLi)
        }
    }

    Tree.prototype.expand = function (tree, parent) {
        var expandedEvent = $.Event(Event.expanded)

        if (this.options.accordion) {
            var openMenuLi = parent.siblings(Selector.open)
            var openTree   = openMenuLi.children(Selector.treeviewMenu)
            this.collapse(openTree, openMenuLi)
        }

        parent.addClass(ClassName.open)
        tree.slideDown(this.options.animationSpeed, function () {
            $(this.element).trigger(expandedEvent)
        }.bind(this))
    }

    Tree.prototype.collapse = function (tree, parentLi) {
        var collapsedEvent = $.Event(Event.collapsed)

        tree.find(Selector.open).removeClass(ClassName.open)
        parentLi.removeClass(ClassName.open)
        tree.slideUp(this.options.animationSpeed, function () {
            tree.find(Selector.open + ' > ' + Selector.treeview).slideUp()
            $(this.element).trigger(collapsedEvent)
        }.bind(this))
    }

  // Private

    Tree.prototype._setUpListeners = function () {
        var that = this

        $(this.element).on('click', this.options.trigger, function (event) {
            that.toggle($(this), event)
        })
    }

  // Plugin Definition
  // =================
    function Plugin(option)
    {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data(DataKey)

            if (!data) {
                var options = $.extend({}, Default, $this.data(), typeof option == 'object' && option)
                $this.data(DataKey, new Tree($this, options))
            }
        })
    }

    var old = $.fn.tree

    $.fn.tree             = Plugin
    $.fn.tree.Constructor = Tree

  // No Conflict Mode
  // ================
    $.fn.tree.noConflict = function () {
        $.fn.tree = old
        return this
    }

  // Tree Data API
  // =============
    $(window).on('load', function () {
        $(Selector.data).each(function () {
            Plugin.call($(this))
        })
    })

    }(jQuery)

