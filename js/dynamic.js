/**
 * Created by lubchik on 6/2/2017.
 */
function DynamicCall() {
    //TODO: Change to something dynamic


        formName = "formname";
        formId = "Some form id optional";
        formparams = "Some params";
        formaction = "formaction";

        var senddata = {
            'action': 'DynamicRequest',
            'formaction': formaction,
            'myurl': window.location.href,
            'formParameters': {
                'formId': formId,
                'formparams': formparams
            }

        };
        var funccall = function (result) {
            if (result.search("error") > -1) {
                alert(result);
                //do something with error
            }
            else {


                //do something with success : result
            }
        }
        DynamicapplyToServer(funccall, funccall, senddata);


}

function DynamicapplyToServer(successcall, failcall, data) {
    var myurl = Dynamic_getUrl();
    $.ajax({
        url: myurl,
        type: 'POST',
        data: data,
        success: successcall,
        fail: failcall
    });
}


function Dynamic_getUrl() {
    if (document.URL.indexOf("wp-admin") != -1) {
        return "admin-ajax.php";
    }
    else {
        if (document.URL.indexOf("index.php") != -1) {
            mylength = document.URL.indexOf("index.php");
            url = document.URL.substring(0, mylength - 1);
            return url + "wp-admin/admin-ajax.php";
        }
        else {
            if (document.URL.indexOf("?") != -1) {
                mylength = document.URL.indexOf("?");
                url = document.URL.substring(0, mylength - 1);
                return url + "wp-admin/admin-ajax.php";

            }
            else {
                return document.URL + "wp-admin/admin-ajax.php"
            }

        }
    }
}
