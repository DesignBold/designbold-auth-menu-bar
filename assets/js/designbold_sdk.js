/*
+ convert json data to html
+ build logout
+ 
*/
var DBMN = DBMN || {};
DBMN.tokenExpire = {};
DBMN.userInfoAPI = {};

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
    'app_secret' : dbtopbarconfig.options.app_secret,
    'redirect_url' : dbtopbarconfig.pluginUrl,
    'internal_debug' : false,
    'scope' : '*.*'
}

DBMN.designbold_login = function(){
    var w = '600';
    var h = '400';
    var title = 'Designbold login';
    var url = dbtopbarconfig.options.app_redirect_url + '&db_action=connect';
    DBMN.popupwindow(url, title, w, h);
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
    var access_token = DBMN.getCookie('access_token');
    var refresh_token = DBMN.getCookie('refresh_token');

    if(typeof access_token != "undefined" && access_token != null && access_token != "" && typeof refresh_token !== "undefined" && refresh_token != null && refresh_token != ""){
        // Kiểm tra xem access_token đã hết hạn hay chưa. nếu trả về 204 là đã hết hạn
        var expires = DBMN.checkAccessTokenExpires(access_token);

        expires.then(function(result_expires){
            // console.log(result_expires);
        })
        .then(function(result2){
            // Retreive access token
            var new_access_token = DBMN.getCookie('access_token');

            if(new_access_token){
                $('#designbold_login_nav').removeClass("d-sm-block");
                // DBMN.btn_res_login.style.display = "none";
                // DBMN.btn_res_signup.style.display = "none";
                DBMN.getUserInfo(DBMN.getCookie('access_token'));
            }
        })
        .catch(function(rej){
            // Reject of access token invalid
            if(rej == 204){
                DBMN.refreshToken(refresh_token);
            }

            // Reject no create access token
            if(rej == 500){
                DBMN.refreshToken(refresh_token);
            }
        });
    }
}

// Check access token expires
DBMN.checkAccessTokenExpires = function(access_token){
    return new Promise (function (resolve, reject){
        var data = "access_token=" + access_token + "&undefined=";
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = false;

        xhr.addEventListener("readystatechange", function () {
            if (this.readyState === 4) {
                if(xhr.status == 200){
                    resolve(this.response);
                }else{
                    return reject(this.status);
                }
            }
        });

        xhr.open("POST", "https://accounts.designbold.com/v2/oauth/tokeninfo");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(data);
    });
}

// Thực hiện DBMN.refreshToken nếu kết quả trả về khác 200, 406 thì retry 5 lần 
// sau 5 lần không được thì xóa hết token cookie (coi như đã logout).
DBMN.i_refresh = 0;
DBMN.refreshToken = function(refresh_token){
    var rfToken = new Promise (function (resolve, reject){
        var data = "app_key=" + DBMN.app.app_key + "&redirect_uri=" + DBMN.app.redirect_url + "&app_secret=" + DBMN.app.app_secret + "&grant_type=refresh_token&refresh_token=" + refresh_token + "&undefined=";

        var xhr = new XMLHttpRequest();
        xhr.withCredentials = false;

        xhr.addEventListener("readystatechange", function () {
            if (this.readyState === 4) {
                // 406 : refresh_token expires
                if(xhr.status == 200){
                    resolve(this.response);
                }else{
                    return reject(this.status);
                }
            }
        });

        xhr.open("POST", "https://accounts.designbold.com/v2/oauth/token");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.send(data);
    });

    rfToken.then(function(value){
        var obj = JSON.parse(value);
        if(obj.access_token){
            DBMN.setCookie ('access_token', obj.access_token, 1);
        }
    })
    .catch(function(rej){
        if(rej == 406){
            if(DBMN.i_refresh <= 2){
                console.log(DBMN.i_refresh);
                DBMN.i_refresh++;
                DBMN.refreshToken(refresh_token);
            }else{
                DBMN.delete_cookie('access_token');
                DBMN.delete_cookie('refresh_token');
                location.reload();
            }
        }
    })
}

DBMN.getUserInfo = function(access_token){

    var userInfo = new Promise (function (resolve, reject){
        var data = null;

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
        xhr.send(data);
    });

    userInfo.then(function(value){
        DBMN.userInfoAPI = JSON.parse(value);
        var user_template = _.template($('#db_user_nav_tmpl').html());
        
        $('#designbold_user_info').html(user_template({
            user : DBMN.userInfoAPI.response.user,
        })).show();
        $('#designbold_login_nav').removeClass("d-sm-block");
    })
    .catch(function(rej){
        console.log(rej);
    })
}

window.signUpComplete = function(access_token, refresh_token){
    DBMN.setCookie ('access_token', access_token, 1);
    DBMN.setCookie ('refresh_token', refresh_token, 1095);
    location.reload();
}

DBMN.logout = function(){
    DBMN.delete_cookie('access_token');
    DBMN.delete_cookie('refresh_token');
    location.reload();
}

DBMN.checkLogin();

