
 let em = document.getElementById('apiemail');
 let ps = document.getElementById('apipassword');
 let apitoken = $("#apitoken");

 
function generate(){

    let email = em.value;
    let password = ps.value;
   
    var url = "http://localhost:90/api/auth/token-access";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", url);

    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
          data = (xhr.responseText);
          localStorage.setItem ("apitoken",data)
          
          if(xhr.status == 400){
            setTexterro();
          }else{
            setText();
          }

      }};

    var data = `{
      "email":"${email}",
      "password":"${password}"
    }`;

    xhr.send(data);
}

function setText(){
  let res = document.getElementById('res');
  let res1 = document.getElementById('res1');
  let res2 = document.getElementById('res2');
  let data = localStorage.getItem("apitoken");
  var firstKey = JSON.parse(data);
  let dtt = (firstKey.access_token).toString();
  apitoken.val(dtt);

  res.classList.remove("actv");
  res1.classList.remove("actv");
  res2.classList.remove("actv");
 
}


function setTexterro(){
  let res = document.getElementById('res');
  let res1 = document.getElementById('res1');
  let res2 = document.getElementById('res2');
  data = localStorage.getItem("apitoken");
  json = JSON.parse(data);
  resdata = json;

  var myArray = [];
  for(var i = 0, len = json.length; i < len; i++){
    myArray.push(json[i][0]+"\n");
  }
  res.classList.remove("actv");
  res1.classList.remove("actv");
  res2.classList.remove("actv");

  if(myArray[0] == undefined){
    res.classList.remove("actv");
   
  }else{
    res.classList.add("actv");
    res.innerText = myArray[0];
  }

  if(myArray[1] == undefined){
    res1.classList.remove("actv");
    
  }else{
    res1.classList.add("actv");
    res1.innerText = myArray[1];
  }


  if(myArray[2] == undefined){
    res2.classList.remove("actv");
    
  }else{
    res2.classList.add("actv");
    res2.innerText = myArray[2];}
  
}