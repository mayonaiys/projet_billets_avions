

function ajaxRequest(type, url, callback, data = null){

    let xhr = new XMLHttpRequest();

    xhr.open(type, url);

    if(type === "POST"){
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }else{
        if(data != null){
            url+="?type="+data;
        }
    }

    xhr.onload = () => {
        httpErrors(xhr.status);
        callback(xhr.responseText);
    };

    if(type === "POST"){
        xhr.send(data);
    }else{
        xhr.send();
    }

}

function httpErrors(errorCode){
    switch (errorCode) {
        case 200:
        case 201:
        case 400: window.open("Bad Request");
            break;
        case 401: window.open("Unauthorized");
            break;
        case 403: window.open("Forbidden");
            break;
        case 404: window.open("Not Found");
            break;
        case 500: window.open("Server Error");
            break;
        case 503: window.open("Server Unavailable");
             break;
             default:
             }
}