(function(){
/**
 * namespace
 * @const
 */
var montblanc = {};
/**
 * String namespace
 * @const
 */
montblanc.string = {};
/**
 * @param {string} str
 * @param {string} prefix
 * @return {boolean}
 */
montblanc.string.startsWith = function(str, prefix) {
    return str.lastIndexOf(prefix, 0) == 0;
};
/**
 * @param {string} str
 */
montblanc.string.urlDecode = function(str) {
    return decodeURIComponent(str.replace(/\+/g, ' '));
};
/**
 * @param {Document} context
 * constructor
 */
montblanc.Cookies = function(context){
    this.document_ = context;
};
/**
 * @type {RegExp}
 * @private
 */
montblanc.Cookies.SPLIT_RE_ = /\s*;\s*/;
/**
 * @return {string}
 * @private
 */
montblanc.Cookies.prototype.getCookie_ = function(){
    return this.document_.cookie;
};
/**
 * @return {Array}
 * @private
 */
montblanc.Cookies.prototype.getParts_ = function(){
    return (this.getCookie_() || '').split(montblanc.Cookies.SPLIT_RE_);
};
/**
 * @param {string} name
 * @param {string} opt_default
 * @return {string|undefined}
 */
montblanc.Cookies.prototype.get = function(name, opt_default){
    var nameEq = name + '=';
    var parts = this.getParts_();
    for (var i = 0, part; part = parts[i]; i++) {
        if (montblanc.string.startsWith(part, nameEq)) {
            return part.substr(nameEq.length);
        }
        if (part == name) {
            return '';
        }
    }
    return opt_default;
};
/**
 * @type {montblanc.Cookies}
 */
montblanc.cookies = new montblanc.Cookies(document);
/**
 * DOM namespace
 * @const
 */
montblanc.dom = {};
/**
 * @param {string} name
 * @return {Element}
 */
montblanc.dom.createElement = function(name){
    return document.createElement(name);
};
/**
 * @param {string} src
 * @param {=Boolean} opt_async
 * @return {Element}
 */
montblanc.dom.createScript = function(src, opt_async){
    var el = montblanc.dom.createElement('script');
    el.src = src;
    el.type = 'text/javascript';
    el.async = !!opt_async;
    return el;
};
/**
 * @param {Element} script
 */
montblanc.dom.injectScript = function(script){
    var fscript = document.getElementsByTagName('script')[0];
    fscript.parentNode.insertBefore(script, fscript);
};


/** **/
montblanc.COOKIE_KEY = '_montblanc';
montblanc.PROTOCOL = 'https:' == document.location.protocol ? 'https:' : 'http:';
montblanc.SYNC_URL = montblanc.PROTOCOL + '//montblanc.rambler.ru/mb';
montblanc.sync = function(){
    montblanc.dom.injectScript(
        montblanc.dom.createScript(montblanc.SYNC_URL, true));
};
montblanc.getParts_ = function(){
    var val = montblanc.cookies.get(montblanc.COOKIE_KEY, '');
    return montblanc.string.urlDecode(val).split('&');
};
/**
 * @param {string} key
 * @param {string} opt_default
 * @return {string|undefined}
 */
montblanc.get = function(key, opt_default){
    var keyEq = key + '=';
    var parts = montblanc.getParts_();
    for (var i = 0, part; part = parts[i]; i++){
        if (montblanc.string.startsWith(part, keyEq)) {
            return montblanc.string.urlDecode(part.substr(keyEq.length));
        }
        if (part == key) {
            return ''
        }
    }
    return opt_default;
};
montblanc.sync();

//api
window['_montblanc'] = {};
window['_montblanc']['get'] = montblanc.get;

})();