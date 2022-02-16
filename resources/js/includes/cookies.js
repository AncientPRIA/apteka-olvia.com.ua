// Set cookie
module.exports = {
    set_cookie: function set_cookie(cookie_name, cookie_val, expires_hours){
        if(typeof expires_hours === 'undefined'){
            expires_hours = -1;
        }
        var expires;
        var path;
        path = '; path=/';
        if(expires_hours !== -1){
            var d = new Date();
            d.setTime(d.getTime() + (expires_hours * 60 * 60 * 1000));
            expires = "; expires="+d.toUTCString();
        }else{
            expires = ";";
        }
        document.cookie = cookie_name+"="+cookie_val+path+expires;
    }
};

