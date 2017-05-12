/// <reference path="../Scripts/jquery-2.1.0-vsdoc.js" />

(function (window, $) {

	'use strict';

	// #region Utilities
	$.isDefined = function (value) {
		return typeof value !== 'undefined';
	};
	$.isString = function (value) {
		return typeof value === 'string';
	};
	$.isNotEmptyString = function (value) {
		return $.isString(value) && value !== "";
	};
	$.isNumber = function (value) {
		return typeof value === 'number';
	};
	$.isBoolean = function (value) {
		return typeof value === 'boolean';
	};
	$.isArray = Array.isArray;

	$.replaceVars = function (str, vars) {
		return str.replace(/{{(.*?)[\|\|.*?]?}}/gi, function (match) {
			match = match.substring(2, match.length - 2).split('||');
			var val = "";
			match.forEach(function (key) {
				if ($.isDefined(vars[key]))
					val = vars[key];
			});
			return val;
		});
	};

	$.valueVars = function (obj, vars) {
		for (var prop in obj) {
			if ($.isString(obj[prop]))
				obj[prop] = $.replaceVars(obj[prop], vars);
		}

		return obj;
	};

	$.fn.findAll = function (selector) {
		var elems = $(this);
		return elems.filter(selector).add(elems.find(selector));
	};

	$.extend($.fn.dataTableExt.oSort, {
		"date-uk-pre": function (a) {
			var ukDatea = a.split('/');
			return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
		},

		"date-uk-asc": function (a, b) {
			return ((a < b) ? -1 : ((a > b) ? 1 : 0));
		},

		"date-uk-desc": function (a, b) {
			return ((a < b) ? 1 : ((a > b) ? -1 : 0));
		}
	});

	String.prototype.escapeHTML = function () {
		return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	};
	// #endregion

	// #region Functions
	function dataFor(varKey, varValue, data) {
		if (varValue && data)
			data[varKey] = varValue;
		else if ($.isPlainObject(varValue))
			data = varValue;
		else
			data = { [varKey]: varValue };

		return data;
	}

	function uniqueId() {
		return (new Date).getTime();
	}
	// #endregion

	// #region Animation
	/**
	 * Animation
	 */
	(function () {
		function animate(element, dir, val, fade, duration, easing, complete) {
			var properties = (($.isDefined(fade) && fade === false) ? {} : { opacity: val });
			$.extend(properties, (dir === 'x') ? { width: val, marginLeft: val, marginRight: val } : { height: val, marginTop: val, marginBottom: val });

			if ($.isDefined(fade) && !$.isBoolean(fade))
				element.animate(properties, fade, duration, easing);
			else
				element.animate(properties, duration, easing, complete);
		}

		$.fn.showX = function (fade, duration, easing, complete) {
			animate($(this), 'x', 'show', fade, duration, easing, complete);
		}
		$.fn.showY = function (fade, duration, easing, complete) {
			animate($(this), 'y', 'show', fade, duration, easing, complete);
		}
		$.fn.hideX = function (fade, duration, easing, complete) {
			animate($(this), 'x', 'hide', fade, duration, easing, complete);
		}
		$.fn.hideY = function (fade, duration, easing, complete) {
			animate($(this), 'y', 'hide', fade, duration, easing, complete);
		}
		$.fn.toggleX = function (fade, duration, easing, complete) {
			animate($(this), 'x', 'toggle', fade, duration, easing, complete);
		}
		$.fn.toggleY = function (fade, duration, easing, complete) {
			animate($(this), 'y', 'toggle', fade, duration, easing, complete);
		}
	})();
	// #endregion


	var WMS = {};

	/**
	 * jQuery
	 */
	WMS.$ = {
		ajax: $.ajax,
		window: $(window),
		document: $(document),
		body: $('#body'),
		wrapper: $('#wrapper'),
		navigationTop: $('#navigation-top'),
		contentWrapper: $('#contentWrapper'),
		loadingSpinner: $('#loading-spinner'),
		logo: $('#logo'),
		navbarTop: $('#navbar-top'),
		navbarTopToggle: $('#navbar-top-toggle'),
		navbarTopLeft: $('#navbar-top-left'),
		navbarUserName: $('#navbar-user-name')
	}

	/**
	 * Document
	 */
	WMS.Document = {};
	WMS.Document.defaultTitle = "WMS";
	WMS.Document.setTitle = function (title) {
		document.title = WMS.Document.defaultTitle + (($.isNotEmptyString(title)) ? " - " + title : "");
	}
	WMS.Document.extractContainer = function (data, fragment) {
		var obj = {}, fullDocument = /<html/i.test(data);

		var $head, $body;
		if (fullDocument) {
			$head = $($.parseHTML(data.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0], document, true));
			$body = $($.parseHTML(data.match(/<body[^>]*>([\s\S.]*)<\/body>/i)[0], document, true));
			if (!fragment) fragment = 'body';
		} else {
			$head = $body = $($.parseHTML(data, document, true));
		}

		if ($body.length === 0)
			return obj;

		obj.title = $head.findAll('title').last().text();

		if (fragment) {
			var $fragment = (fragment === 'body') ? $body : $body.findAll(fragment).first();

			if ($fragment.length) {
				obj.contents = (fragment === 'body') ? $fragment : $fragment.contents();

				if (!obj.title)
					obj.title = $fragment.attr('title') || $fragment.data('title');
			}

		} else if (!fullDocument) {
			obj.contents = $body;
		}

		// Clean up any <title> tags
		if (obj.contents) {
			// Remove any parent title elements
			obj.contents = obj.contents.not(function () { return $(this).is('title') });

			// Then scrub any titles from their descendants
			obj.contents.find('title').remove();

			// Gather all script[src] elements
			obj.scripts = obj.contents.findAll('script[src]').remove();
			obj.contents = obj.contents.not(obj.scripts);
		}

		// Trim any whitespace off the title
		if (obj.title) obj.title = $.trim(obj.title);

		return obj;
	}
	WMS.Document.executeScriptTags = function (scripts) {
		if (!scripts) return;

		var existingScripts = $('script[src]');

		scripts.each(function () {
			var src = this.src;
			var matchedScripts = existingScripts.filter(function () {
				return this.src === src;
			});
			if (matchedScripts.length) return;

			var script = document.createElement('script');
			var type = $(this).attr('type');
			if (type) script.type = type;
			script.src = $(this).attr('src');
			document.head.appendChild(script);
		});
	}
	WMS.Document.replaceContents = function (context, container, options) {
		if ($.isFunction(options))
			options = { complete: options }

		WMS.Document.setTitle(container.title);
		context.stop(true).fadeOut(function () {
			if ($.isFunction(options.beforeReplace)) options.beforeReplace();

			context.html(container.contents).fadeIn(function () {
				if ($.isFunction(options.complete)) options.complete();
			});

			if ($.isFunction(options.afterReplace)) options.afterReplace();
		});
	}
	WMS.Document.cloneContents = function (container) {
		var cloned = container.clone();
		// Unmark script tags as already being eval'd so they can get executed again
		// when restored from cache. HAXX: Uses jQuery internal method.
		cloned.find('script').each(function () {
			if (!this.src) jQuery._data(this, 'globalEval', false);
		});
		return [container.selector, cloned.contents()];
	}

	/**
	 * UI
	 */
	WMS.UI = {};

	WMS.UI.loadingSpinner = new (function (selector) {
		var $spinner = $(selector);
		var queue = 0;
		var disable = false;

		this.show = function () {
			if (queue <= 0 && !disable)
				$spinner.stop(true).fadeIn();
			queue++;
		};

		this.hide = function () {
			queue--;
			if (queue <= 0 && !disable)
				$spinner.stop(true).fadeOut();
		};

		this.disable = function () {
			$spinner.stop(true).hide();
			disable = true;
		}

		this.enable = function () {
			if (queue > 0)
			$spinner.stop(true).show();
			disable = false;
		}

	})(WMS.$.loadingSpinner);

	WMS.UI.notify = function (message, options) {
		if ($.isString(message))
			message = { message: message };

		options = $.extend({}, message, (($.isDefined(options)) ? (($.isString(options) || $.isNumeric(options)) ? { status: options } : options) : {}));

		var defaults = {
			message: "",
			status: "info",
			icon: true,
			timeout: 5000,
			pos: 'bottom-left',
			onClose: $.noop
		};

		options = $.extend({}, defaults, options);

		if ($.isNumeric(options.status)) {
			if (options.status <= 0)
				options.status = 'danger';
			else
				switch (options.status) {
					case 1:
						options.status = 'success';
						break;
					case 3:
						options.status = 'warning';
						break;
					default:
						options.status = 'info';
				}
		}

		if (options.icon) {
			if (!$.isString(options.icon)) {
				switch (options.status) {
					case 'success':
						options.icon = 'check-circle';
						break;
					case 'info':
						options.icon = 'info-circle';
						break;
					case 'warning':
						options.icon = 'exclamation-circle';
						break;
					case 'danger':
						options.icon = 'times-circle';
						break;
				}
			}

			options.message = "<i class=\"fa fa-" + options.icon + "\"></i> " + options.message;
		}

		$.UIkit.notify(options);
	}

	/**
	 * Ajax
	 */
	WMS.ajax = (function () {
		function parseURL(url, hash) {
			var a = document.createElement('a');
			a.href = url;
			if (hash) a.hash = hash;
			return a;
		}

		function locationReplace(url) {
			window.history.replaceState(null, "", ajax.state.url);
			window.location.replace(url);
		}

		function abortXHR(xhr) {
			if (xhr && xhr.readyState < 4) {
				xhr.onreadystatechange = $.noop;
				xhr.abort();
			}
		}

		function stripHash(location) {
			return location.href.replace(/#.*/, '');
		}

		// TODO: Add ajax push setting and implementation to pjax function
		var ajax = function (url, options) {
			options = $.extend(true, {}, $.ajaxSettings, ajax.settings, dataFor('url', url, options));

			if ($.isFunction(options.url))
				options.url = options.url();

			//var target = options.target
			var pjax = options.push || options.replace;
			var hash = parseURL(options.url).hash;

			var context = options.context = $(options.container);

			if (pjax && !context.length)
				throw "no container for pjax";
			// We want the browser to maintain two separate internal caches: one
			// for pjax'd partial page loads and one for normal page loads.
			// Without adding this secret parameter, some browsers will often
			// confuse the two.
			//if (!options.data) options.data = {}
			//if ($.isArray(options.data)) {
			//	options.data.push({ name: '_ajax', value: context.selector })
			//} else {
			//	options.data._ajax = context.selector
			//}

			function fire(type, args, props) {
				if (!context.length) return;
				if (!props) props = {};
				//props.relatedTarget = target
				var event = $.Event(type, props);
				context.trigger(event, args);
				return !event.isDefaultPrevented();
			}

			var timeoutTimer;

			var beforeSendFn = options.beforeSend;
			options.beforeSend = function (xhr, settings) {
				xhr.setRequestHeader('X-AJAX', 'true');
				if (pjax)
					xhr.setRequestHeader('X-PJAX', 'true');
				if (context.length)
					xhr.setRequestHeader('X-AJAX-Container', context.selector);

				if (($.isFunction(beforeSendFn) && beforeSendFn(xhr, settings) === false) | fire('ajax:beforeSend', [xhr, settings]) === false)
					return false;

				// No timeout for non-GET requests
				// Its not safe to request the resource again with a fallback method.
				if (settings.type !== 'GET')
					settings.timeout = 0;

				if (settings.timeout > 0) {
					timeoutTimer = setTimeout(function () {
						if (fire('ajax:timeout', [xhr, options]))
							xhr.abort('timeout');
					}, settings.timeout);

					// Clear timeout setting so jquerys internal timeout isn't invoked
					settings.timeout = 0;
				}

				options.requestTarget = parseURL(settings.url, hash);
				options.requestUrl = options.requestTarget.href;

				WMS.UI.loadingSpinner.show();
			}

			var completeFn = options.complete;
			options.complete = function (xhr, textStatus) {
				if (timeoutTimer)
					clearTimeout(timeoutTimer);

				fire('ajax:complete', [xhr, textStatus, options]);
				//fire('pjax:end', [xhr, options])
				if ($.isFunction(completeFn)) completeFn(xhr, textStatus, options);
				WMS.UI.loadingSpinner.hide();
			}

			var errorFn = options.error;
			options.error = function (xhr, textStatus, errorThrown) {
				//var container = extractContainer("", xhr, options)
				var allowed = (($.isFunction(errorFn) && errorFn(xhr, textStatus, errorThrown, options) === true) | fire('ajax:error', [xhr, textStatus, errorThrown, options]) === true);
				if (!allowed && options.type == 'GET' && textStatus !== 'abort' && pjax) {
					setTimeout(function () { locationReplace(container.url); }, 2000);
					WMS.UI.notify("Error... The page will reload!", { status: 'danger' });
				} else if (!allowed && textStatus !== 'abort') {
					WMS.UI.notify("Error... Please try again!", { status: 'danger' });
				}
			}

			var successFn = options.success;
			var beforeReplaceFn = options.beforeReplace;
			var afterReplaceFn = options.afterReplace;
			var replaceCompleteFn = options.replaceComplete;
			options.success = function (data, status, xhr) {
				// If $.pjax.defaults.version is a function, invoke it first.
				// Otherwise it can be a static string.
				//var currentVersion = (typeof $.pjax.defaults.version === 'function') ?
				//  $.pjax.defaults.version() :
				//  $.pjax.defaults.version
				//var latestVersion = xhr.getResponseHeader('X-PJAX-Version')

				//var url = options.requestUrl;

				// If there is a layout version mismatch, hard load the new url
				//if (currentVersion && latestVersion && currentVersion !== latestVersion) {
				//	locationReplace(container.url)
				//	return
				//}

				// If the new response is missing a body, hard load the page
				//if (!container.contents) {
				//	locationReplace(container.url)
				//	return
				//}

				if (context.length) {
					var container = WMS.Document.extractContainer(data, options.fragment); //extractContainer(data, xhr, options)
					var previousState = ajax.state;

					if (pjax) {
						ajax.state = {
							id: options.id || uniqueId(),
							url: options.requestUrl,
							timeout: options.timeout,
							title: container.title,
							container: context.selector,
							fragment: options.fragment
							//popstate: popstateFn,
							//beforeReplace: beforeReplaceFn,
							//afterReplace: afterReplaceFn,
							//replaceComplete: replaceCompleteFn
						}
						window.history.replaceState(ajax.state, container.title || "", options.requestUrl);
					}

					// Only blur the focus if the focused element is within the container.
					var blurFocus = $.contains(options.container, document.activeElement);
					// Clear out any focused controls before inserting new page contents.
					if (blurFocus) {
						try {
							document.activeElement.blur();
						} catch (e) { }
					}

					//if (container.title) document.title = container.title;

					fire('ajax:beforeReplace', [container, options], pjax ? { state: ajax.state, previousState: previousState } : {})
					if ($.isFunction(beforeReplaceFn)) beforeReplaceFn(container, options);
					WMS.Document.replaceContents(context, container, {
						afterReplace: function () {
							fire('ajax:afterReplace', [container, options])
							if ($.isFunction(afterReplaceFn)) afterReplaceFn(container, options);
						},
						complete: function () {
							// FF bug: Won't autofocus fields that are inserted via JS.
							// This behavior is incorrect. So if theres no current focus, autofocus
							// the last field.
							//
							// http://www.w3.org/html/wg/drafts/html/master/forms.html
							var autofocusEl = context.find('input[autofocus], textarea[autofocus]').last()[0];
							if (autofocusEl && document.activeElement !== autofocusEl)
								autofocusEl.focus();

							WMS.Document.executeScriptTags(container.scripts);

							var scrollTo = options.scrollTo;

							// Ensure browser scrolls to the element referenced by the URL anchor
							if (hash) {
								var name = decodeURIComponent(hash.slice(1));
								var target = document.getElementById(name) || document.getElementsByName(name)[0];
								if (target) scrollTo = $(target).offset().top;
							}

							if ($.isNumber(scrollTo)) WMS.$.window.scrollTop(scrollTo);

							fire('ajax:replaceComplete', [container, options])
							if ($.isFunction(replaceCompleteFn)) replaceCompleteFn(container, options);
						}
					});
				}

				fire('ajax:success', [data, status, xhr, options])
				if ($.isFunction(successFn)) successFn(data, status, xhr, options);
			}

			if (pjax) {
				// Initialize pjax.state for the initial page load. Assume we're
				// using the container and options of the link we're loading for the
				// back button to the initial page. This ensures good back button
				// behavior.
				if (!ajax.state) {
					ajax.state = {
						id: uniqueId(),
						url: window.location.href,
						title: document.title,
						container: context.selector,
						fragment: options.fragment,
						timeout: options.timeout
					};
					window.history.replaceState(ajax.state, ajax.state.title);
				}

				// Cancel the current request if we're already pjaxing
				abortXHR(ajax.xhr);
			}

			//pjax.options = options
			var xhr = WMS.$.ajax(options);

			if (pjax) {
				ajax.xhr = xhr;

				if (xhr.readyState > 0 && options.push && !options.replace) {
					// Cache current container element before replacing it
					cachePush(ajax.state.id, WMS.Document.cloneContents(context));

					window.history.pushState(null, "", options.requestUrl);
				}
			}

			return xhr;
		}

		ajax.settings = {
			timeout: 0,
			push: false,
			replace: false,
			//type: 'GET',
			//dataType: 'html',
			scrollTo: false,
			maxCacheLength: 25,
			//version: findVersion
		}

		ajax.action = function (url, action, options) {
			var data = dataFor('action', action, options.data); //$.extend({ action: action }, options.data);

			ajax({
				type: "POST",
				dataType: "json",
				url: url,
				data: data,
				success: function (respond) {
					if (($.isDefined(options.notify) ? options.notify : true) && $.isDefined(respond.msg) && $.isDefined(respond.result))
						WMS.UI.notify(respond.msg, respond.result);

					if ($.isFunction(options.success)) options.success(respond);
				},
				error: function () {
					if ($.isFunction(options.error)) options.error();
				},
				complete: function () {
					if ($.isFunction(options.complete)) options.complete();
				}
			});
		}

		$.fn.$ajaxForm = $.fn.ajaxForm;

		function ajaxForm(options) {
			return $(this).each(function () {
				var thisForm = $(this);
				var opts = $.extend({}, ajax.formSettings, options);
				opts.url = opts.url || thisForm.attr('action') || "/";
				opts.type = opts.type || thisForm.attr('method') || "POST";
				var formInputs, formSubmit, oldSubmitText;

				var finishFunc = function (respond) {
					var s = $.isDefined(respond.result) && (respond.result > 0);

					if (opts.notify && $.isDefined(respond.msg) && $.isDefined(respond.result) && respond.result >= 0)
						WMS.UI.notify(respond.msg, respond.result);

					formSubmit.html(s ? opts.successText : opts.errorText);
					window.setTimeout(function () {
						formSubmit.html(oldSubmitText);
						formInputs.prop("disabled", false);
					}, 5000);

					if (s && $.isFunction(opts.success)) opts.success(respond);
					if (!s && $.isFunction(opts.error)) opts.error(respond);
				};

				thisForm.$ajaxForm({
					url: opts.url,
					type: opts.type,
					dataType: opts.dataType,
					data: opts.data,
					beforeSerialize: function ($form) {
						thisForm = $form;
						formInputs = $(':input', thisForm).add(opts.inputs);
						formSubmit = $(':submit', thisForm).add(opts.submit);
					},
					beforeSubmit: function () {
						oldSubmitText = formSubmit.html();
						formInputs.prop("disabled", true);
						formSubmit.html(opts.loadingText);
					},
					success: function (respond) {
						finishFunc(respond);
					},
					error: function () {
						finishFunc({ msg: "Error... Please try again", result: -1 });
					}
				});
			});
		}

		ajax.formSettings = {
			dataType: "json",
			loadingText: "Loading...",
			successText: "Success",
			errorText: "Error!",
			notify: true
		}

		ajax.getPage = function (url, success, data) {
			ajax({
				url: url,
				data: data,
				success: success
			});
		}
		ajax.loadPage = function (url, container, success, push) {
			url = url || (location.pathname + location.search + location.hash);
			container = container || WMS.$.contentWrapper.selector;
			(push ? pjax : ajax)({
				url: url,
				container: container,
				success: success
			});
		}

		var pjax = function (url, options) {
			return ajax($.extend({}, pjax.settings, dataFor('url', url, options)))
		}

		pjax.settings = {
			timeout: 0,
			push: true,
			replace: false,
			type: 'GET',
			dataType: 'html',
		}

		// When called on a container with a selector, fetches the href with
		// ajax into the container or with the data-pjax attribute on the link
		// itself.
		//
		// Tries to make sure the back button and ctrl+click work the way
		// you'd expect.
		//
		// Exported as $.fn.pjax
		//
		// Accepts a jQuery ajax options object that may include these
		// pjax specific options:
		//
		//
		// container - Where to stick the response body. Usually a String selector.
		//             $(container).html(xhr.responseBody)
		//             (default: current jquery context)
		//      push - Whether to pushState the URL. Defaults to true (of course).
		//   replace - Want to use replaceState instead? That's cool.
		//
		// For convenience the second parameter can be either the container or
		// the options object.
		//
		// Returns the jQuery object
		function fnPjax(selector, container, options) {
			var context = this;
			return this.on('click.ajax', selector, function (event) {
				var opts = $.extend({}, dataFor('container', container, options));
				if (!opts.container)
					opts.container = $(this).attr('data-ajax-container') || context;
				handleClick(event, opts);
			});
		}

		// Public: pjax on click handler
		//
		// Exported as $.pjax.click.
		//
		// event   - "click" jQuery.Event
		// options - pjax options
		//
		// Examples
		//
		//   $(document).on('click', 'a', $.pjax.click)
		//   // is the same as
		//   $(document).pjax('a')
		//
		//  $(document).on('click', 'a', function(event) {
		//    var container = $(this).closest('[data-pjax-container]')
		//    $.pjax.click(event, container)
		//  })
		//
		// Returns nothing.
		function handleClick(event, container, options) {
			options = dataFor('container', container, options);

			var link = event.currentTarget;

			if (link.tagName.toUpperCase() !== 'A')
				throw "ajax click requires an anchor element";

			// Middle click, cmd click, and ctrl click should open
			// links in a new tab as normal.
			if (event.which > 1 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey)
				return;

			// Ignore cross origin links
			if (location.protocol !== link.protocol || location.hostname !== link.hostname)
				return;

			// Ignore case when a hash is being tacked on the current URL
			if (link.href.indexOf('#') > -1 && stripHash(link) == stripHash(location))
				return;

			// Ignore event with default prevented
			if (event.isDefaultPrevented())
				return;

			var defaults = {
				url: link.href,
				container: $(link).attr('data-ajax-container'),
				target: link
			}

			var opts = $.extend({}, defaults, options);
			var clickEvent = $.Event('ajax:click');
			$(link).trigger(clickEvent, [opts]);

			if (!clickEvent.isDefaultPrevented()) {
				pjax(opts);
				event.preventDefault();
				$(link).trigger('ajax:clicked', [opts]);
			}
		}

		// Public: pjax on form submit handler
		//
		// Exported as $.pjax.submit
		//
		// event   - "click" jQuery.Event
		// options - pjax options
		//
		// Examples
		//
		//  $(document).on('submit', 'form', function(event) {
		//    var container = $(this).closest('[data-pjax-container]')
		//    $.pjax.submit(event, container)
		//  })
		//
		// Returns nothing.
		function handleSubmit(event, container, options) {
			options = dataFor(container, options)

			var form = event.currentTarget
			var $form = $(form)

			if (form.tagName.toUpperCase() !== 'FORM')
				throw "$.pjax.submit requires a form element"

			var defaults = {
				type: ($form.attr('method') || 'GET').toUpperCase(),
				url: $form.attr('action'),
				container: $form.attr('data-pjax'),
				target: form
			}

			if (defaults.type !== 'GET' && window.FormData !== undefined) {
				defaults.data = new FormData(form);
				defaults.processData = false;
				defaults.contentType = false;
			} else {
				// Can't handle file uploads, exit
				if ($(form).find(':file').length) {
					return;
				}

				// Fallback to manually serializing the fields
				defaults.data = $(form).serializeArray();
			}

			pjax($.extend({}, defaults, options))

			event.preventDefault()
		}

		var initialPop = true;
		var initialURL = window.location.href;
		var initialState = window.history.state;

		if (initialState && initialState.container)
			ajax.state = initialState;
		if ('state' in window.history)
			initialPop = false;

		function onPjaxPopstate(event) {

			// Hitting back or forward should override any pending PJAX request.
			if (!initialPop) {
				abortXHR(ajax.xhr);
			}

			var previousState = ajax.state;
			var state = event.state;
			var direction;

			if (state && state.container) {
				// When coming forward from a separate history session, will get an
				// initial pop with a state we are already at. Skip reloading the current
				// page.
				if (initialPop && initialURL == state.url) return;

				if (previousState) {
					// If popping back to the same state, just skip.
					// Could be clicking back from hashchange rather than a pushState.
					if (previousState.id === state.id) return;

					// Since state IDs always increase, we can deduce the navigation direction
					direction = previousState.id < state.id ? 'forward' : 'back';
				}

				var cache = cacheMapping[state.id] || [];
				var context = $(cache[0] || state.container), contents = cache[1];
				
				if (context.length) {
					var container = { title: state.title, contents: contents };
					if (previousState) {
						// Cache current container before replacement and inform the
						// cache which direction the history shifted.
						cachePop(direction, previousState.id, WMS.Document.cloneContents(context));
					}

					//if ($.isFunction(state.popstate)) state.popstate(state, direction);
					context.trigger($.Event('ajax:popstate', {
						state: state,
						direction: direction
					}));

					var options = {
						id: state.id,
						url: state.url,
						container: context,
						push: false,
						replace: true,
						fragment: state.fragment,
						timeout: state.timeout,
						scrollTo: false
					}

					if (contents) {
						ajax.state = state;
						context.trigger($.Event('ajax:beforeReplace', {
							state: state,
							previousState: previousState
						}), [contents, options]);

						WMS.Document.replaceContents(context, container, {
							afterReplace: function () {
								context.trigger($.Event('ajax:afterReplace'), [contents, options]);
							},
							complete: function () {
								context.trigger($.Event('ajax:replaceComplete'), [contents, options]);
							}
						});

						//if (state.title) document.title = 
						//container.html(contents)

						//context.trigger('ajax:end', [null, options]);
					} else {
						ajax(options);
					}

					// Force reflow/relayout before the browser tries to restore the
					// scroll position.
					context[0].offsetHeight
				} else {
					locationReplace(location.href)
				}
			}
			initialPop = false
		}

		var cacheMapping = {};
		var cacheForwardStack = [];
		var cacheBackStack = [];

		// Push previous state id and container contents into the history
		// cache. Should be called in conjunction with `pushState` to save the
		// previous container contents.
		//
		// id    - State ID Number
		// value - DOM Element to cache
		//
		// Returns nothing.
		function cachePush(id, value) {
			cacheMapping[id] = value;
			cacheBackStack.push(id);

			// Remove all entries in forward history stack after pushing a new page.
			trimCacheStack(cacheForwardStack, 0);

			// Trim back history stack to max cache length.
			trimCacheStack(cacheBackStack, ajax.settings.maxCacheLength);
		}

		// Shifts cache from directional history cache. Should be
		// called on `popstate` with the previous state id and container
		// contents.
		//
		// direction - "forward" or "back" String
		// id        - State ID Number
		// value     - DOM Element to cache
		//
		// Returns nothing.
		function cachePop(direction, id, value) {
			var pushStack, popStack;
			cacheMapping[id] = value;

			if (direction === 'forward') {
				pushStack = cacheBackStack;
				popStack = cacheForwardStack;
			} else {
				pushStack = cacheForwardStack;
				popStack = cacheBackStack;
			}

			pushStack.push(id);
			if (id = popStack.pop())
				delete cacheMapping[id];

			// Trim whichever stack we just pushed to to max cache length.
			trimCacheStack(pushStack, ajax.settings.maxCacheLength);
		}

		// Trim a cache stack (either cacheBackStack or cacheForwardStack) to be no
		// longer than the specified length, deleting cached DOM elements as necessary.
		//
		// stack  - Array of state IDs
		// length - Maximum length to trim to
		//
		// Returns nothing.
		function trimCacheStack(stack, length) {
			while (stack.length > length)
				delete cacheMapping[stack.shift()];
		}

		// Add the state property to jQuery's event object so we can use it in
		// $(window).bind('popstate')
		if ($.inArray('state', $.event.props) < 0)
			$.event.props.push('state');


		$.ajax = ajax;
		$.fn.pjax = fnPjax;
		$.fn.ajaxForm = ajaxForm;
		WMS.pjax = pjax;
		WMS.pjax.click = handleClick;
		WMS.pjax.submit = handleSubmit;

		WMS.$.window.on('popstate.ajax', onPjaxPopstate);

		return ajax;
	})();

	/**
	 * User
	 */
	WMS.User = {};
	WMS.User.CheckLogin = function (success, goHome) {
		WMS.ajax.action('/User', 'loginCheck', {
			notify: false,
			success: function (respond) {
				WMS.User.isLoggedIn = respond.isLoggedIn;
				WMS.User.navbarItems = respond.navbarItems;
				WMS.User.data = respond.data;

				var navItems = "";
				var userName = "";
				if (WMS.User.isLoggedIn) {
					WMS.User.navbarItems.forEach(function (item) {
						navItems += '<li><a href="' + item.link + '">' + item.label + '</a></li>'
					});
					WMS.$.logo.attr('href', '/');
					userName = WMS.User.data.namePrefix + WMS.User.data.firstName;
					//if (!goHome)
					//	WMS.ajax.loadPage();
				} else {
					WMS.$.logo.attr('href', 'javascript:void(0)');
					//if (!goHome)
					//	WMS.ajax.loadPage('/User/login');
				}

				if (goHome)
					WMS.ajax.loadPage('/', null, null, true);
				else
					WMS.ajax.loadPage();

				var showNav, fadeNav;
				if (WMS.Window.isSmall)
					showNav = WMS.$.navbarTop, fadeNav = WMS.$.navbarTopToggle;
				else
					showNav = WMS.$.navbarTopToggle, fadeNav = WMS.$.navbarTop;

				fadeNav.fadeOut({
					complete: function () {
						WMS.$.navbarTopLeft.html(navItems);
						WMS.$.navbarUserName.text(userName);
						if (WMS.User.isLoggedIn)
							fadeNav.fadeIn({
								start: function () {
									showNav.show();
									WMS.Window.resize();
									WMS.UI.loadingSpinner.enable();
								}
							});
					},
					done: function () {
						WMS.Window.resize();
						if (WMS.User.isLoggedIn)
							WMS.UI.loadingSpinner.disable();
					}
				});

				if ($.isFunction(success)) success(respond);
			}
		});
	}
	WMS.User.Signout = function () {
		bootbox.confirm({
			title: "<i class=\"fa fa-sign-out\"></i> Sign out",
			message: "Are you sure that you want to sign out?",
			buttons: {
				confirm: {
					label: "<i class=\"fa fa-sign-out\"></i> Sign out",
				}
			},
			callback: function (result) {
				if (result) {
					WMS.ajax.action('/User', 'logout', {
						success: function () { WMS.User.CheckLogin(null, true); },
						error: WMS.User.CheckLogin
					});
				}
			}
		});
	}

	/**
	 * Window
	 */
	WMS.Window = {};
	WMS.Window.load = function () {
		WMS.Window.resize();
		WMS.$.navigationTop.pjax('a:not([data-action]):not([data-toggle]):not([disabled]):not([data-download-url])', WMS.$.contentWrapper);
		WMS.$.contentWrapper.pjax('a:not([data-action]):not([data-toggle]):not([disabled]):not([data-download-url]):not([data-ajax-container="contentWrapper"])', '#page-wrapper');
		WMS.$.contentWrapper.pjax('a[data-ajax-container="contentWrapper"]', WMS.$.contentWrapper);

		WMS.$.contentWrapper.on('click', 'a[data-download-url], button[data-download-url]', function (e) {
			var $this = $(this);
			var $thisData = $this.data();
			$.fileDownload($thisData.downloadUrl, $.extend({}, $thisData.downloadOptions || {}, {
				httpMethod: $thisData.downloadMethod || 'POST',
				data: $.extend({ _ignoreAjax: 1 }, $thisData.downloadAction ? { action: $thisData.downloadAction } : {}, $thisData.downloadData || {}),
				failCallback: function () {
					WMS.UI.notify("Error... Please try again!", { status: 'danger' });
				}
			}));
		});

		WMS.$.navigationTop.find('a[data-action]').click(function (e) {
			e.preventDefault();
			if ($(this).data('action') == "signout") {
				WMS.User.Signout();
			}
		});

		WMS.User.CheckLogin();
	}
	WMS.Window.resize = function () {
		WMS.Window.height = WMS.$.window.height();
		WMS.Window.width = WMS.$.window.width();
		WMS.Window.isSmall = WMS.Window.width <= 767;

		if (WMS.Window.isSmall || !WMS.$.navbarTop.is(":visible"))
			WMS.$.loadingSpinner.insertAfter(WMS.$.logo);
		else
			WMS.$.loadingSpinner.insertAfter(WMS.$.navbarTopLeft);
	}

	WMS.$.window.load(WMS.Window.load);
	$.fn.dataTable.defaults.bAutoWidth = false;
	$.fn.dataTable.defaults.column.mRender = $.fn.dataTable.render.text();
	WMS.$.window.resize(WMS.Window.resize);

	window.WMS = WMS;
})(this, this.jQuery);