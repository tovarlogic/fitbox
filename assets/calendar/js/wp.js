if (($.browser.msie) && (($.browser.version == '7.0') || ($.browser.version == '8.0'))) {
    if (top != self)
        top.location.href = location.href;

}

function _redirect(url) {
    if ((($.browser.msie) && (($.browser.version == '7.0') || ($.browser.version == '8.0'))) || top == self) {
        window.location.href = url;
        
    } else {       
        top.redirect(url);
        
    }
}

