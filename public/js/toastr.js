/**************************************************************************************
Title: toastr.js
Description: A javascript library to show android style toast notifications on web.
Developer URI: https://www.voidtricks.com
Documentation URI: https://www.voidtricks.com/toastr/

**************************************************************************************/
function Toastr(opt = {}) {
    this.defaults = {
        theme: '',
        timeout: 2000,
        animation: 'fade',
        position: 'bottom',
        autohide: true
    };
    this.extend(this.defaults, opt);
    this.node = '';
}

Toastr.prototype.extend = function(a, b) {
    for(var key in b)
        if(b.hasOwnProperty(key))
            a[key] = b[key];
    return a;
}

Toastr.prototype.show = function(text = '') {
    if(document.getElementsByClassName("toastrWrap").length > 0){
        document.getElementsByClassName("toastrWrap")[0].remove();
    }

    this.node = document.createElement("div");
    this.node.innerHTML = text;
    this.node.className = 'toastrWrap '+this.defaults.theme+' '+this.defaults.animation+' '+this.defaults.position;

    document.getElementsByTagName("body")[0].insertBefore(this.node, document.body.firstChild);
    var el = document.getElementsByClassName("toastrWrap")[0];
    el.style.marginLeft = '-' + (el.offsetWidth / 2) + 'px';
    el.classList.add("show");
    if(this.defaults.autohide){
        this.remove(el, this.defaults.timeout);
    }
}

Toastr.prototype.hide = function(){
    if(document.getElementsByClassName("toastrWrap").length > 0){
        var el = document.getElementsByClassName("toastrWrap")[0];
        el.classList.remove("show");
        setTimeout(() => {
            el.remove();
        }, 300);
    }
}

Toastr.prototype.remove = function(el, ti){
    setTimeout(() => {
        el.classList.remove("show");
    }, ti);
    setTimeout(() => {
        el.remove();
    }, ti+300);
}