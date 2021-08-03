<?php 
require "functions.php"; 
$db = new dbexec();
if(isset($_REQUEST['export']) && isset($_REQUEST['url']))
{
    $jsondata = file_get_contents($_REQUEST['url']);
    $jsonans = json_decode($jsondata, true);
    $csv = 'export.csv';
    $file_pointer = fopen($csv, 'w');
    foreach($jsonans as $i){
        fputcsv($file_pointer, $i);
    }
    fclose($file_pointer);
}
?>
<!DOCTYPE html>
<html>
<head>
<style>
table, th, td, tr {
    border: 1px solid #dddddd
}
</style>
<title>Baju DF Managements</title>
<script>
const http = new XMLHttpRequest();
const sizeArr = ["XS","S","M","L","XL","2XL","3XL","4XL","5XL"];
let totalSizeA = {"XS":0,"S":0,"M":0,"L":0,"XL":0,"2XL":0,"3XL":0,"4XL":0,"5XL":0};
let totalSizeB = {"XS":0,"S":0,"M":0,"L":0,"XL":0,"2XL":0,"3XL":0,"4XL":0,"5XL":0};
let sleeveA = {"short":0,"long":0};
let sleeveB = {"short":0,"long":0};
    function resultTable()
    {
        http.open("GET","current_records.php",true);
        http.send();
        http.onreadystatechange = function()
        {
            if(this.readyState==4 && this.status==200)
            {
                var total = 0;
                var obj = JSON.parse(this.responseText);
                var table = "<table style='text-align:center;'><tr><th>No.</th><th>Customer ID</th><th>Shopee User</th><th>Customer Name</th><th>Package</th><th>Size</th><th>Sleeve</th><th>Quantity</th><th>Price</th><th>Date</th></tr>";
                for(var i=0;i<obj.length;i++)
                {
                    total += parseInt(obj[i].Price);
                    if(obj[i].Package=="A")
                    {
                        if(sizeArr[i]==obj[i].Size && obj[i].Sleeve=="short")
                        {
                                sleeveA[obj[i].Sleeve] += 1;
                                totalSizeA[obj[i].Size] += 1;
                        }
                        else
                        {
                                sleeveA[obj[i].Sleeve] += 1;
                                totalSizeA[obj[i].Size] += 1;
                        }
                    }
                    else
                    {
                        if(sizeArr[i]==obj[i].Size && obj[i].Sleeve=="short")
                        {
                                sleeveB[obj[i].Sleeve] += 1;
                                totalSizeB[obj[i].Size] += 1;
                        }
                        else
                        {
                                sleeveB[obj[i].Sleeve] += 1;
                                totalSizeB[obj[i].Size] += 1;
                        }
                    }
                    table += "<tr><td>"+(i+1)+"</td><td>" + obj[i].Customer_Id + "</td><td>"+obj[i].Shopee_User+
                             "</td><td>"+obj[i].Customer_Name+"</td><td>"+obj[i].Package+"</td><td>"+obj[i].Size+
                             "</td><td>"+obj[i].Sleeve+"</td><td>"+obj[i].Quantity+"</td><td>"+obj[i].Price+
                             "</td><td>"+obj[i].Date+"</td></tr>";
                }
                document.getElementById("table1").innerHTML = table + "</table>";
                document.getElementById("total").innerHTML = "Total Price : RM"+total;
            }
        }
    }
    function sendPost()
    {
        let id = document.forms['add']['id'].value;
        let shopee = document.forms['add']['shopee'].value;
        let name = document.forms['add']['name'].value;
        let package = document.forms['add']['package'].value;
        let quantity = document.forms['add']['quantity'].value;
        let sleeve = document.forms['add']['sleeve'].value;
        let size = document.forms['add']['size'].value;
        let date = document.forms['add']['date'].value;
        if(id=="" || shopee=="" || name=="" || package=="" || quantity=="" || size=="" || date=="")
        {
            event.preventDefault();
            alert("Make sure you filled all the blanks");
        }
        else
        {
            var params = "add=records&id="+id+"&shopee="+shopee+"&name="+name+"&package="+package+"&quantity="+quantity+"&size="+size+"&sleeve="+sleeve+"&date="+date;
            http.open("POST","new_records.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.onreadystatechange = function(){
                if(this.readyState==4 && this.status==200)
                {
                    document.getElementById("result").innerHTML = "<center><font size='14px'>" + this.responseText + "</font></center>";
                }
            }
            http.send(params);
            event.preventDefault();
            setTimeout(function(){
                resultTable();
                totalSizeA = {"XS":0,"S":0,"M":0,"L":0,"XL":0,"2XL":0,"3XL":0,"4XL":0,"5XL":0};
                totalSizeB = {"XS":0,"S":0,"M":0,"L":0,"XL":0,"2XL":0,"3XL":0,"4XL":0,"5XL":0};
                sleeveA = {"short":0,"long":0};
                sleeveB = {"short":0,"long":0};
                document.getElementById("formSubmit").reset();
            },
            100)
            setTimeout(function(){
                document.getElementById("result").innerHTML = "";
            },
            3000)
        }
    }
function selectPackage() 
{
  var a = document.getElementById("a").selected;
  var b = document.getElementById("b").selected;
  var t = document.getElementById("table2");
  if(a==true)
  {
      t.innerHTML = "<table><tr><th>SHORT</th><th>LONG</th></tr><tr><td>"+sleeveA['short']+"</td><td>"+sleeveA['long']+"</td></tr></table>";
      t.innerHTML += "<table><th>XS</th><th>X</th><th>M</th><th>L</th><th>XL</th><th>2XL</th><th>3XL</th><th>4XL</th><th>5XL</th>"+
                     "<tr><td>"+totalSizeA['XS']+"</td><td>"+totalSizeA['S']+"</td><td>"+totalSizeA['M']+"</td><td>"+totalSizeA['L']+
                     "</td><td>"+totalSizeA['XL']+"</td><td>"+totalSizeA['2XL']+"</td><td>"+totalSizeA['3XL']+"</td><td>"+totalSizeA['4XL']+
                     "</td><td>"+totalSizeA['5XL']+"</td></tr></table>";
  }
  else if(b==true)
  {
      t.innerHTML = "<table><tr><th>SHORT</th><th>LONG</th></tr><tr><td>"+sleeveB['short']+"</td><td>"+sleeveB['long']+"</td></tr></table>";
      t.innerHTML += "<table><th>XS</th><th>X</th><th>M</th><th>L</th><th>XL</th><th>2XL</th><th>3XL</th><th>4XL</th><th>5XL</th>"+
                     "<tr><td>"+totalSizeB['XS']+"</td><td>"+totalSizeB['S']+"</td><td>"+totalSizeB['M']+"</td><td>"+totalSizeB['L']+
                     "</td><td>"+totalSizeB['XL']+"</td><td>"+totalSizeB['2XL']+"</td><td>"+totalSizeB['3XL']+"</td><td>"+totalSizeB['4XL']+
                     "</td><td>"+totalSizeB['5XL']+"</td></tr></table>";
  }
  else
  {
    t.innerHTML = "";
  }
}
function exportCSV()
{
    http.open("GET","index.php?export=&url=http://localhost/baju/current_records.php",true);
    http.onreadystatechange = function() {
        if(this.readyState==4 && this.status==200)
        {
            alert("Sucessfully export the data");
        }
    }
    http.send();
}
    resultTable();
</script>
</head>


<body background="http://i66.servimg.com/u/f66/14/86/38/04/ground10.gif" bgcolor="#000000">
<style>#content-wrapper{width:50%; margin:15px auto; padding:10px; text-align:left; border:1px solid #06ff00}</style>
<div id='content-wrapper'>
<center>
<font style="font: 15pt Arial; color: yellow;">DragonForce Shopee Management System </font><br></br>
<img src="https://scontent.fkul14-1.fna.fbcdn.net/v/t1.6435-9/227544855_249206010376576_5899128555875464662_n.jpg?_nc_cat=105&ccb=1-3&_nc_sid=730e14&_nc_eui2=AeF4E_hT9FRY-ED4t0lrLZ1s3T8W6MITgwvdPxbowhODC4diOgOF5hq60YBdgQzdnXkqz0-lKXXozD8i9_ZQRY4B&_nc_ohc=SoWk_ktuTcIAX-A7zGV&_nc_ht=scontent.fkul14-1.fna&oh=e9eb253b1b2afae21490681fbd153dcc&oe=613080C7" width="550" height="320">
</div>
<style>
#content-wrapper2{
    width:85%;
    margin:15px auto;
    padding:10px;
    text-align:left;
    border:1px solid #06ff00;
    overflow:auto;
}
.submit{
    width:50%;
    margin:auto;
}
.submit input[type=submit]{
    width:100%;
    font-size:17px;
    cursor:pointer;
    border-radius:5px;
    margin-top:10px;
}
#content-wrapper2 table,
#content-wrapper2 th,
#content-wrapper2 td,{
    border:1px solid #fff;
}
#content-wrapper2 table{
    border-collapse: collapse;
    margin:auto;
    margin-top:20px;
}
#stats{
    width:50%;
    margin:auto;
    text-align:center;
}
#tt h3{
    float:right;
}
.ft{
    width:50%;
    margin:auto;
    text-align:center;
    margin-top:15px;
}
.ft button{
    width:30%;
    font-size:17px;
    cursor:pointer;
}
</style>
<div id='content-wrapper2'>
<form id='formSubmit' name='add' onsubmit='sendPost()' method='POST' > 

<table style="color:yellow;text-align:center;"><th>ID</th><th>User</th><th>Name</th><th>Quantity</th><th>Package</th><th>Size</th><th>Sleeve</th><th>Date</th>
<tr>
<td><input type='text' name='id' placeholder='shopee ID'></td>
<td><input type='text' name='shopee' placeholder='Shopee User'></td>
<td><input type='text' name='name' placeholder='Customer Name'></td>
<td><input type='number' name='quantity' placeholder='1'></td>
<td><select name='package'>
<?php 
$package = $db::Package();
while($p=$package->fetch_assoc())
{
    echo "<option value='".$p['package_id']."'>".$p['type']."</option>";
}
 ?>
</select></td>
<td><select name='size'>
<?php 
$package = $db::PackageSize();
while($p=$package->fetch_assoc())
{
    echo "<option value='".$p['size_id']."'>".$p['size']."</option>";
}
 ?>
</select></td>
<td><select name='sleeve'>
<?php 
$package = $db::Sleeves();
while($p=$package->fetch_assoc())
{
    echo "<option value='".$p['sid']."'>".$p['stype']."</option>";
}
 ?>
</select></td>
<td><input type='date' name='date' placeholder=''></td>
</tr>
</table>
<section class='submit'>
<input type='submit' name='add' value='Submit'>
</form>
</section>


<span style="color: yellow;" id='result'></span>

<span style="color: yellow;" id='table1'></span>

</br>
<div id='stats'>
<label style="color: yellow;">Choose Package</label>
<select>
  <option id="default" selected>--Package--</option>
  <option id="a">Package A</option>
  <option id="b">Package B</option>
</select>
<button onclick="selectPackage()">View Statistic</button>
</div>
</br>
<span style="color: yellow;" id='table2'></span>

<div id='tt'>
    <h3><span style="color: yellow;" id='total'></span></h3>
</div>

<footer class='ft'>
<button onclick="exportCSV()">Export CSV</button>
</footer>
</body>
</html>
