$(document).ready(function(){
	
/**
 * Sweep the page for bindings indicated by HTML attribute hooks
 * 
 * Class any DOM element with event handlers.
 * Place a 'bind' attribute in the element in need of binding.
 * bind="focus.revealPic blur.hidePic" would bind two methods
 * to the object; the method named revealPic would be the focus handler
 * and hidePic would be the blur handler. All bound handlers
 * receive the event object as an argument
 * 
 * Version 2
 * 
 * @param {string} target a selector to limit the scope of action
 * @returns The specified elements will be bound to handlers
 */
function bindHandlers(target) {
    if (typeof(target) == 'undefined') {
        var targets = $('*[bind*="."]');
    } else {
		var targets = $(target).find('*[bind*="."]')
	}
	targets.each(function(){
		var bindings = $(this).attr('bind').split(' ');
		for (i = 0; i < bindings.length; i++) {
			var handler = bindings[i].split('.');
			if (typeof(window[handler[1]]) === 'function') {
				// handler[0] is the event type
				// handler[1] is the handler name
				$(this).off(handler[0]).on(handler[0], window[handler[1]]);
			}
		}
	});
}

/**
 * new jquery function to center something in the scrolled window
 * 
 * Sets the css left and top of the chained element
 */
jQuery.fn.center = function() {
//    this.css("position", "fixed");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
            $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
            $(window).scrollLeft()) + "px");
    return this;
}

/**
 * Toggle an element
 * 
 * @param string element selector of the element to toggle
 * @returns {undefined}
 */
//function toggleThis(element) {
//    $(element).toggle(50, function() {
//        // animation complete.
//    });
//}

/**
 * Set up the click on a node to control the display-toggle of another node
 * 
 * Any <item class=toggle id=unique_name> will toggle <item class=unique_name> on click
 */
function initToggles() {
    $('.toggle').unbind('click').bind('click', function(e) {
		var id = e.currentTarget.id;
        $('.' + $(this).attr('id')).toggle(50, function() {
            // animation complete.
			if (typeof(statusMemory) == 'function') {
				statusMemory(id, e);
			}
        });
    })
}

function initToggleHits() {
    $('.hit').trigger('click');
}

/**
 * Create 'dropzones' for image upload
 * 
 * Not a generic process. This will make specific, tailored zones for specific 
 * nodes when they exist. This is the simplest way to customize the action 
 * that will handle the upload
 * 
 * @returns {undefined}
 */
//function initDropzone(params) {
//	
//	var artwork_image = $('fieldset.artwork-image');
//	if (artwork_image.length > 0){
//		artwork_image.append($("div#myId").dropzone({ url: "/file/post" }));
//	}
//}

    initToggles();
    initToggleHits();
	bindHandlers();
//	initDropzone({
//		target: 'fieldset.artwork-image',
//		node_id: 'artwork-image-zone'
//	});
	
	//Foundation JavaScript
	// Documentation can be found at: http://foundation.zurb.com/docs
	$(document).foundation();
	//var elem = new Foundation.Dropdown('div.top-bar-left > ul');
	//var elem = new Foundation.Dropdown('#example-dropdown');

	Dropzone.options.artworkStack = { // The camelized version of the ID of the form element

	  // The configuration we've talked about above
	  autoProcessQueue: false,
	  uploadMultiple: false,
	  parallelUploads: 1,
	  maxFiles: 1,
	  previewsContainer: 'div.dropzone-previews',
	  hiddenInputContainer: 'fieldset.artwork-image',
	  paramName: "image[image_file]",
	  dictDefaultMessage: 'Drop Files Here',

	  // The setting up of the dropzone
	  init: function() {
		var myDropzone = this;

		// First change the button to actually tell Dropzone to process the queue.
		this.element.querySelector("input[type=submit]").addEventListener("click", function(e) {
		  // Make sure that the form isn't actually being sent.
		  e.preventDefault();
		  e.stopPropagation();
		  myDropzone.processQueue();
		});

		// Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
		// of the sending event because uploadMultiple is set to true.
		this.on("sendingmultiple", function() {
		  // Gets triggered when the form is actually being sent.
		  // Hide the success button or the complete form.
		});
		this.on("successmultiple", function(files, response) {
		  // Gets triggered when the files have successfully been sent.
		  // Redirect user or notify of success.
		});
		this.on("errormultiple", function(files, response) {
		  // Gets triggered when there was an error sending the files.
		  // Maybe show form again, and notify user of error
		});
	  }

	}

});

/**
 * Create 'dropzones' for image upload
 * 
 * Not a generic process. This will make specific, tailored zones for specific 
 * nodes when they exist. This is the simplest way to customize the action 
 * that will handle the upload
 * 
 * @returns {undefined}
 */
function initDropzone(params) {
	var settings = {
			position : 'prepend', // must be a jQuery insertion method
			target : 'fieldset.image',
			url: '/artworks/upload',
			drop_node: '<div class="dropzone"></div>',
			node_id: 'dropzone',
	};
	settings = mergeObjects(settings, params);
	var artwork_image = $(settings.target);
	if (artwork_image.length > 0){
		var drop_node = $(settings.drop_node).attr('id', settings.node_id).dropzone({ url: settings.url, params: {thing: 'one'} });
		artwork_image[settings.position](drop_node);
//		drop_node.on('addedFile', function() {alert('added');});
	}
}

/**
Returns a new object containing all of the properties of all the supplied
objects. The properties from later objects will overwrite those in earlier
objects.
 
Passing in a single object will create a shallow copy of it. For a deep copy,
use `clone()`.
 
@method merge
@param {Object} objects* One or more objects to merge.
@return {Object} A new merged object.
**/
var mergeObjects = function () {
    var i      = 0,
        len    = arguments.length,
        result = {},
        key,
        obj;
 
    for (; i < len; ++i) {
        obj = arguments[i];
 
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                result[key] = obj[key];
            }
        }
    }
 
    return result;
};
//**********************

//function maximize() {
//	var zone_tool = $('a#maximize');
//	var edit_zone = $('.edit-zone');
//	var preview_zone = $('.preview-zone');
//	
//	if (zone_tool.html() == 'Expand') {
//		edit_zone.data('w', edit_zone.css('width'));
//		preview_zone.data('w', preview_zone.css('width'));
//		edit_zone.css('width', '100%');
//		preview_zone.css('width', '100%');
//		zone_tool.html('Reduce');
//		
//	} else {
//		edit_zone.css('width', edit_zone.data('w'));
//		preview_zone.css('width', preview_zone.data('w'));
//		zone_tool.html('Expand');
//	}
//}

