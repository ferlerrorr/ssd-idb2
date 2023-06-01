let passwordinput = document.getElementById("password");
let repasswordinput = document.getElementById("password_confirmation");

let password = passwordinput.value;
let password_confirmation = repasswordinput.value;

// let headersList = {
//     Accept: "*/*",
//     "Content-Type": "application/json",
// };

// let bodyContent = JSON.stringify({
//     password: password,
//     password_confirmation: password_confirmation,
// });

// let response = await fetch(
//     "http://127.0.0.1:131/api/ssd/new-password/" + email,
//     {
//         method: "POST",
//         body: bodyContent,
//         headers: headersList,
//     }
// );

// let data = await response.text();
// localStorage.setItem("response", data);
// setText();

async function passReset() {
    console.log(getCookieValue(""));
    let email = getCookieValue("ssd_api_email");
    function getCookieValue(cookieName) {
        var cookies = decodeURIComponent(document.cookie).split(";");

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            var cookieParts = cookie.split("=");
            var name = cookieParts[0];
            var value = cookieParts[1];

            if (name === cookieName) {
                return decodeURIComponent(value);
            }
        }

        return "";
    }

    // Usage example
    let password = passwordinput.value;
    let password_confirmation = repasswordinput.value;

    let headersList = {
        "Content-Type": "application/json",
    };

    let bodyContent = JSON.stringify({
        password: password,
        password_confirmation: password_confirmation,
    });

    let response = await fetch("/api/ssd/new-password/" + email, {
        method: "POST",
        body: bodyContent,
        headers: headersList,
    });

    let data = await response.text();
    localStorage.setItem("response", data);
    setText();

    // if (response.status == 200) {
    //     setTimeout(function () {
    //         window.open("http://localhost:90/token");
    //     }, 1200);
    // }
}

function setText() {
    let res = document.getElementById("res");
    let res1 = document.getElementById("res1");
    let res2 = document.getElementById("res2");
    data = localStorage.getItem("response");
    json = JSON.parse(data);
    resdata = json;

    var myArray = [];
    for (var i = 0, len = json.length; i < len; i++) {
        myArray.push(json[i][0] + "\n");
    }
    res.classList.remove("actv");
    res1.classList.remove("actv");
    res2.classList.remove("actv");

    if (myArray[0] == undefined) {
        res.classList.remove("actv");
    } else {
        res.classList.add("actv");
        res.innerText = myArray[0];
    }

    if (myArray[1] == undefined) {
        res1.classList.remove("actv");
    } else {
        res1.classList.add("actv");
        res1.innerText = myArray[1];
    }

    if (myArray[2] == undefined) {
        res2.classList.remove("actv");
    } else {
        res2.classList.add("actv");
        res2.innerText = myArray[2];
    }
}
