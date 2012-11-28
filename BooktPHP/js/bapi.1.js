;
/*!
  * $script.js v1.3
  * https://github.com/ded/script.js
  * Copyright: @ded & @fat - Dustin Diaz, Jacob Thornton 2011
  * Follow our software http://twitter.com/dedfat
  * License: MIT
  */
!function(a,b,c){function t(a,c){var e=b.createElement("script"),f=j;e.onload=e.onerror=e[o]=function(){e[m]&&!/^c|loade/.test(e[m])||f||(e.onload=e[o]=null,f=1,c())},e.async=1,e.src=a,d.insertBefore(e,d.firstChild)}function q(a,b){p(a,function(a){return!b(a)})}var d=b.getElementsByTagName("head")[0],e={},f={},g={},h={},i="string",j=!1,k="push",l="DOMContentLoaded",m="readyState",n="addEventListener",o="onreadystatechange",p=function(a,b){for(var c=0,d=a.length;c<d;++c)if(!b(a[c]))return j;return 1};!b[m]&&b[n]&&(b[n](l,function r(){b.removeEventListener(l,r,j),b[m]="complete"},j),b[m]="loading");var s=function(a,b,d){function o(){if(!--m){e[l]=1,j&&j();for(var a in g)p(a.split("|"),n)&&!q(g[a],n)&&(g[a]=[])}}function n(a){return a.call?a():e[a]}a=a[k]?a:[a];var i=b&&b.call,j=i?b:d,l=i?a.join(""):b,m=a.length;c(function(){q(a,function(a){h[a]?(l&&(f[l]=1),o()):(h[a]=1,l&&(f[l]=1),t(s.path?s.path+a+".js":a,o))})},0);return s};s.get=t,s.ready=function(a,b,c){a=a[k]?a:[a];var d=[];!q(a,function(a){e[a]||d[k](a)})&&p(a,function(a){return e[a]})?b():!function(a){g[a]=g[a]||[],g[a][k](b),c&&c(d)}(a.join("|"));return s};var u=a.$script;s.noConflict=function(){a.$script=u;return this},typeof module!="undefined"&&module.exports?module.exports=s:a.$script=s}(this,document,setTimeout)

/* Bookt API */
var BAPI = BAPI || {};

(function(context) {

context.defaultOptions = {
    language: 'en-US',
    currency: 'USD',
    baseURL: 'https://connect.bookt.com',
    apikey: null,
    logging: true,
	jsonp: true	
};

context.entities = {
    property: 'property',
    development: 'development',
    poi: 'poi',
	doctemplate: 'doctemplate',
	leads: 'leads',
	leadactivity: 'leadactivity'
}

context.initialized = false;
context.reqid = null;
context.stime = new Date();
context.etime = new Date();
context.init = function (apikey, options) {
	if (context.initialized) return;
	context.initialized = true;	
    this.defaultOptions.apikey = apikey;
	
	context.reqid =  $("meta[name=REQUESTID]").attr("content");
	if (context.reqid == null) context.reqid = guid();
	
    if (typeof apikey == "undefined" || apikey == null) {
        context.defaultOptions.baseURL = '/misc';
	}
	if (typeof(options) != "undefined" && options != null && typeof(options.baseURL) != "undefined") {
		context.defaultOptions.baseURL = options.baseURL;
		context.defaultOptions.jsonp = false;
	}	
    $.holdReady(true);
	$script("https://booktplatform.s3.amazonaws.com/shared/js/lib/jquery.cookie.js", 'cookie');
	$script.ready('cookie', function() {
		context.loadsession();
				
		// load visit info
		if (context.session().visitorid == null || context.session().visitorid == 0) {
			context.log('Getting client info.');
			$.holdReady(true);
			context.showclientinfo(function(data) { 
				context.session().visitorid = data.visit.VisitorID;	
				if (context.session().personid == null || context.session().personid == 0) {
					context.session().personid = data.visit.PersonID;	
				}
				$.holdReady(false);
			});
		}
		
		// load config info
		if (options != null && options.loadconfig) {
			context.log('Loading config');
			$.holdReady(true);
			context.config.get(apikey, function(data) { 
				$.holdReady(false); 
			});
		}		
	});		
		
	$.holdReady(false);	
}

$(window).load(function() {
	$script.ready('cookie', function() {
		context.session().lasteventtime = new Date();		
		context.savesession();
		// do logging
		context.etime = new Date();
		var t = (context.etime - context.stime);
		context.log('Page took ' + t + 'ms.');
		});
});

/* Session */
function SessionProfile() {
	this.visitorid = 0;
	this.personid = 0;
	this.searchmode = 0;
	this.searchparams = new SearchParams();		
	this.favorites = {
		add: function(id, callback) {			
			var url = context.defaultOptions.baseURL + '/ws/' + '?method=create&entity=favorites&id=' + id;
			context.utils.getJSON2(url, function (data) { callback(data); }, false);	
		},
		del: function(id, callback) {
			var url = context.defaultOptions.baseURL + '/ws/' + '?method=delete&entity=favorites&id=' + id;
			context.utils.getJSON2(url, function (data) { callback(data); }, false);	
		},
		clear: function(callback) {
			var url = context.defaultOptions.baseURL + '/ws/' + '?method=delete&entity=favorites&clearall=1';
			context.utils.getJSON2(url, function (data) { callback(data); }, false);	
		},		
		list: function(callback) {
			var url = context.defaultOptions.baseURL + '/ws/' + '?method=get&entity=favorites';
			context.utils.getJSON2(url, function (data) { callback(data); }, false);	
		}
	}
}
function SearchParams() {
	this.checkin = null;
	this.checkout = null;
	this.los = null;
	this.adults = new MultiSearchParam();
	this.children = new MultiSearchParam();
	this.beds = new MultiSearchParam();
	this.baths = new MultiSearchParam();
	this.sleeps = new MultiSearchParam();
	this.rooms = new MultiSearchParam();
	this.category = null;
	this.location = null;
	this.altid = null;
	this.dev = null;
	this.amenities = null;
	this.headline = null;
	this.rate = new MultiSearchParam();
}		
function MultiSearchParam() {
	this.exactly = null; 
	this.min = null; 
	this.max = null; 
}

var _cookiename = 'BAPI';
var _session = new SessionProfile();
context.session = function() {
	return _session;
}
context.loadsession = function() {		
	var a = JSON.parse($.cookie(_cookiename)) 	
	if (typeof (a) === "undefined" || a == null) {
		context.log("Creating new BAPI session.");
		_session = new SessionProfile();
	}	
	else {
		_session.visitorid = a.visitorid;
		_session.personid = a.personid;
		_session.searchmode = a.searchmode;
		_session.searchparams.checkin = a.searchparams.checkin;
		_session.searchparams.checkout = a.searchparams.checkout;
		_session.searchparams.los = a.searchparams.los;
		_session.searchparams.adults = (typeof(a.searchparams.adults) === "undefined" || a.searchparams.adults == null ? new MultiSearchParam() : a.searchparams.adults);
		_session.searchparams.children = (typeof(a.searchparams.children) === "undefined" || a.searchparams.children == null ? new MultiSearchParam() : a.searchparams.children);
		_session.searchparams.beds = (typeof(a.searchparams.beds) === "undefined" || a.searchparams.beds == null ? new MultiSearchParam() : a.searchparams.beds);
		_session.searchparams.baths = (typeof(a.searchparams.baths) === "undefined" || a.searchparams.baths == null ? new MultiSearchParam() : a.searchparams.baths);
		_session.searchparams.sleeps = (typeof(a.searchparams.sleeps) === "undefined" || a.searchparams.sleeps == null ? new MultiSearchParam() : a.searchparams.sleeps);
		_session.searchparams.rooms = (typeof(a.searchparams.rooms) === "undefined" || a.searchparams.rooms == null ? new MultiSearchParam() : a.searchparams.rooms);
		_session.searchparams.category = a.searchparams.category;
		_session.searchparams.location = a.searchparams.location;
		_session.searchparams.altid = a.searchparams.altid;
		_session.searchparams.dev = a.searchparams.dev;
		_session.searchparams.amenities = a.searchparams.amenities;
		_session.searchparams.headline = a.searchparams.headline;
		_session.searchparams.rate = (typeof(a.searchparams.rate) === "undefined" || a.searchparams.rate == null ? new MultiSearchParam() : a.searchparams.rate);
	}
}
context.savesession = function() {
	$.cookie(_cookiename, JSON.stringify(_session),  { expires: 30, path: '/' }); 
}	
context.clearsession = function() {	
	_session = new SessionProfile();
	context.savesession();	
}

/* Logging */
context.log = function (a) {
    if (context.defaultOptions.logging) {
        if (typeof console != "undefined")
            console.log(a);
    }
}

/* CRUD */
context.create = function(entity, options, callback) {
}

context.search = function (entity, options, callback) {
    if (typeof (options) === "undefined" || options === null) options = { checkin: null, checkout: null, numbaths: null, numbeds: null, sleeps: null };
       
    var optionsURL = jQuery.param(options);        
    var url = context.defaultOptions.baseURL + '/ws/' + '?method=search' + '&entity=' + entity + '&' + optionsURL;                                            
	context.utils.getJSON2(url, function (data) { callback(data); }, true);	
}

context.get = function (ids, entity, options, callback) {
	if (typeof (ids) === "undefined" || ids === null || ids.length == 0) return null;
    if (typeof (options) === "undefined" || options === null) options = { "empty": true };
	if (typeof (options.page) === "undefined" || options.page == null || options.page <= 0) { options.page = 1; }
	if (typeof (options.pagesize) === "undefined" || options.pagesize == null || options.pagesize <= 0) { options.pagesize = 5; }	
	var si = (options.page-1) * options.pagesize;
	var ei = si + options.pagesize;
	if(ids.length>1){
		var nids = ids.slice(si, ei);
	}
	else{
		var nids = ids;
	}	
	options.page = 1; // always get the first page
    var optionsURL = jQuery.param(options);
    var url = context.defaultOptions.baseURL + '/ws/' + '?method=get' + '&ids=' + nids +
                                            '&entity=' + entity + '&' + optionsURL;                                            
	context.utils.getJSON2(url, function (data) { callback(data); }, true);
}

context.delete = function(ids, entity, options, callback) {
}

context.createevent = function(options, callback) {
	if (callback == null) return null;
    if (typeof (options) === "undefined" || options === null) options = { "empty": true };
    var optionsURL = jQuery.param(options);
    var url = context.defaultOptions.baseURL + '/ws/' + '?method=createevent' + '&' + optionsURL;                                            
	context.utils.getJSON2(url, function (data) { 
		if (data != null && data.result != null) {
			context.session().personid = data.result.Lead.ID;
			context.savesession();
		}
		callback(data); 
	}, false);				
}

var _config;
var _textdata;
context.config = {
	get: function (options, callback) {
		if (typeof (options) === "undefined" || options == null) options = { "empty": true };
		if (typeof (options.forceload) === "undefined" || options.forceload == null) options.forceload = false;
		
		if (!options.forceload) {
			if (_config != null) { 
				if (typeof (callback) === "undefined" || callback == null) 
					return _config;
				callback(_config); 
				return _config;
			}
			if (callback == null) return null;
		}
		
		var optionsURL = jQuery.param(options);
		var url = context.defaultOptions.baseURL + '/ws/' + '?method=getconfig' + '&' + optionsURL;												
		context.utils.getJSON2(url, function (data) { _config = data.result; callback(_config); }, true);	
	},
	
	getrentalagreement: function (options, callback) {
		if (typeof (options) === "undefined" || options == null) options = { "empty": true };
		if (typeof (options.forceload) === "undefined" || options.forceload == null) options.forceload = false;
		
		if (!options.forceload) {
			if (_config != null) { 
				if (typeof (callback) === "undefined" || callback == null) 
					return _config;
				callback(_config); 
				return _config;
			}
			if (callback == null) return null;
		}
		
		var optionsURL = jQuery.param(options);
		var url = context.defaultOptions.baseURL + '/ws/' + '?method=getconfig' + '&' + optionsURL;												
		context.utils.getJSON2(url, function (data) { _config = data.result; callback(_config); }, true);	
	}
}

/* Debug Methods */
context.showclientinfo = function (callback) {
	if (callback == null) return null;
	var url = context.defaultOptions.baseURL + '/ws/' + '?method=showclientinfo';
	context.utils.getJSON2(url, function (data) { callback(data); }, true);				
}

/* Utilities */
context.utils = {
	jsondate: function(s) {
		if (typeof(s) === "undefined" || s == null) return null;
		try { return new Date(parseFloat(/Date\(([^)]+)\)/.exec(s)[1])); }
		catch(err) { return null; }
	},
	loadStyleSheet: function (url) {
		$('head').append('<link rel="stylesheet" type="text/css" href="' + url + '">');		
	},
	loadScript: function (url, callback) {
		$.ajax({
			type: "GET",
			url: url,
			success: callback,
			dataType: "script",
			cache: true,
			async: false
		});		
	},
	getJSON: function (url, callback, cache) {
		$.ajax({
			url: url,
			dataType: 'jsonp',
			success: callback,
			cache: true
		});
	},
	getJSON2: function (url, callback, cache) {	
		url = url + '&apikey=' + context.defaultOptions.apikey 
		url = url + '&callback=?';
		$.getJSON(url, function (data) {
			callback(data); 
		});	
	}
}

function S4() {
   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}
function guid() {
   return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}
function hash(str) {
    var hash = 0;
    for (i = 0; i < str.length; i++) {
        char = str.charCodeAt(i);
        hash = char + (hash << 6) + (hash << 16) - hash;
    }
    return hash;
}

})(BAPI); 
