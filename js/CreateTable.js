function handleClick(state){
  if (state == 2){
    if (this.innerHTML == "Start Emulator"){
      this.innerHTML = "Stop Emulator";
      //CODE TO AFFECT DATABASE ADDED HERE
    }else{
      this.innerHTML = "Start Emulator";
      //CODE TO AFFECT DATABASE ADDED HERE
    }
  }
}

function loadTable(state, data){
  // EXTRACT VALUE FOR HTML HEADER.
  var col = [];
  for (var i = 0; i < data.length; i++) {
      for (var key in data[i]) {
          if (col.indexOf(key) === -1) {
              col.push(key);
          }
      }
  }
  if (state == 2) col.push("Emulator Control");

  // CREATE DYNAMIC TABLE.
  var table = document.createElement("table");

  // CREATE HTML TABLE HEADER ROW USING THE EXTRACTED HEADERS ABOVE.

  var tr = table.insertRow(-1);                   // TABLE ROW.
  var stat = -1;                                  // STATUS COLUMN VALUE

  for (var i = 0; i < col.length; i++) {
      var th = document.createElement("th");      // TABLE HEADER.
      th.innerHTML = col[i];
      if (th.innerHTML == "status"){
        stat = i;
      }
      tr.appendChild(th);
  }


  // ADD JSON DATA TO THE TABLE AS ROWS.
  for (var i = 0; i < data.length; i++) {

      tr = table.insertRow(-1);

      for (var j = 0; j < col.length; j++) {
          var tabCell = tr.insertCell(-1);

          if (state == 2 && j == col.length-1){
              if (data[i][col[1]] ==  "device"){
               tabCell.innerHTML = '<button class = "button" onclick="handleClick.call(this,2);">Stop Emulator</>';
              }else{
               tabCell.innerHTML = '<button class = "button" onclick="handleClick.call(this,2);">Start Emulator</>';
              }
          }
          else tabCell.innerHTML = data[i][col[j]];
      }
  }


  // FINALLY ADD THE NEWLY CREATED TABLE WITH JSON DATA TO A CONTAINER.
  var divContainer = document.getElementById("showData");
  divContainer.innerHTML = "";
  divContainer.appendChild(table);
}

function CreateTableFromJSON(state) {
    //For getting data from server
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "http://localhost:9999/Get_Server_Data.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xmlhttp.onreadystatechange = function() {
          if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
              var myObj = JSON.parse(xmlhttp.responseText);
              loadTable(state, myObj);
          }
        };

        if(state == 1){
          xmlhttp.send("json=" + JSON.stringify({"RequestType":"Submissions"}));
        }
        else if(state == 2){
          xmlhttp.send("json=" + JSON.stringify({"RequestType":"Emulators"}));
        }
}
