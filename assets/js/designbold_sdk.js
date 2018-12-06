var DBMN = DBMN || {};
DBMN.tokenExpire = {};
DBMN.userInfoAPI = {};
DBMN.access_token_default = 'b0f99ceb3d596cb8e7152088548c41e981920c0bd92312047fd8e75b9eee440d';

DBMN.box_login_signup = document.getElementById('designbold_login_nav');
DBMN.btn_login = document.getElementById('designbold_login');
DBMN.btn_signup = document.getElementById('designbold_signup');
DBMN.box_user_info = document.getElementById('designbold_user_info');

// DBMN.btn_res_login = document.getElementById('designbold_res_login');
// DBMN.btn_res_signup = document.getElementById('designbold_res_signup');

function getHostName(url) {
    var match = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/i);
    if (match != null && match.length > 2 && typeof match[2] === 'string' && match[2].length > 0) {
        return match[2];
    }
    else {
        return null;
    }
}

DBMN.app = {
    'app_key' : dbtopbarconfig.options.app_key,
    'redirect_url' : dbtopbarconfig.pluginUrl,
    'internal_debug' : false,
    'scope' : '*.*'
}

// Safari 3.0+ "[object HTMLElementConstructor]" 
DBMN.isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

DBMN.designbold_login = function(){
    // check safari
    if( ! DBMN.isSafari ){
        var w = '600';
        var h = '400';
        var title = 'Designbold login';
        var url = dbtopbarconfig.options.app_redirect_url + '&db_action=connect';
        DBMN.popupwindow(url, title, w, h);
    }else{
        window.location.href = dbtopbarconfig.safari_url;
    }
}

DBMN.popupwindow = function(url, title, w, h){
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

DBMN.getCookie = function(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

DBMN.setCookie = function (cname, cvalue, exdays){
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

DBMN.delete_cookie = function(name) {
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

// Check login when website start
DBMN.checkLogin = function(){
    var access_token = dbtopbarconfig.access_token;
    var refresh_token = dbtopbarconfig.refresh_token;

    if(typeof access_token != "undefined" && access_token != null && access_token != "" && typeof refresh_token !== "undefined" && refresh_token != null && refresh_token != ""){
        $('#designbold_login_nav').removeClass("d-sm-block");
        // DBMN.btn_res_login.style.display = "none";
        // DBMN.btn_res_signup.style.display = "none";
        DBMN.getUserInfo( access_token );
    }else{
        DBMN.getUserInfo( DBMN.access_token_default );
    }
}

DBMN.getUserInfo = function( access_token ){
    if( access_token !== '' ) {
        var userInfo = new Promise (function (resolve, reject){
            var xhr = new XMLHttpRequest();
            xhr.withCredentials = false;

            xhr.addEventListener("readystatechange", function () {
                if (this.readyState === 4) {
                    if(xhr.status == 200){
                        resolve(this.response);
                    }else{
                        reject(this.statusText);
                    }
                }
            });

            xhr.open("GET", "https://api.designbold.com/v3/user/me");
            xhr.setRequestHeader("Authorization", "Bearer " + access_token);
            xhr.send();
        });

        userInfo.then(function(value){
            DBMN.userInfoAPI = JSON.parse(value);
            if (DBMN.userInfoAPI.response.user.hash_id !== 'guest') {
                var user_template = _.template($('#db_user_nav_tmpl').html());
                $('#designbold_user_info').html(user_template({
                    user : DBMN.userInfoAPI.response.account,
                })).show();
            }else{
                var box_login_signup_tmp = _.template($('#db_user_designbold_login_nav_tmpl').html());
                $('#designbold_login_nav').html(box_login_signup_tmp({}));
            }
        })
        .catch(function(rej){
            console.log(rej);
        })
    }
}

window.signUpComplete = function(){
    location.reload();
}

DBMN.logout = function(){
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": dbtopbarconfig.logout_url,
        "method": "GET",
        "headers": {
        "cache-control": "no-cache"
        }
    }

    $.ajax(settings).done(function (response) {
        location.reload();
    });
}

DBMN.checkLogin();

