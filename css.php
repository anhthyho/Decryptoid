<?php  //stylin
header("Content-type: text/css; charset: UTF-8");
$linkColor = "#add8e6";

header("Content-type: text/css");
$font_family = 'Arial, Helvetica, sans-serif';
$grayText = "#444444";
$lightGray = "#a9a9a9";
?>
.central {
	margin: auto;
  	width: 300;
  	background-color: <?=$linkColor?>;
  	font-family: <?=$font_family?>;
  	font-size: 15;
  	padding: 20px;
  	min-height: 400;
}

.answer {
	margin: auto;
  	width: 300;
  	background-color: <?=$linkColor?>;
  	font-family: <?=$font_family?>;
  	font-size: 15;
  	padding: 20px;
  	min-height: 300;
  	text-align: center;
}

.allcenter{
margin: auto;
text-align: center;
}

.box {
  	width: 150;
  	background-color: <?=$lightGray?>;
  	height: auto;
  	margin-bottom: 20px;
  	display: inline-block;
  	text-align: center;
}

.box2 {
  	width: 80%;
  	background-color: <?=$lightGray?>;
  	height: auto;
  	padding-bottom: 10px;
  	margin-bottom: 20px;
  	display: inline-block;
  	text-align: center;
  	word-wrap:break-word;
}

input[type=button], input[type=submit] {
  background-color:  <?=$lightGray?>;
  border: none;
  color: white;
  padding: 15px 25px;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
  font-family: <?=$font_family?>;
  font-size: 13;
}

input[type=text]{
  width: 60%;
    padding: 10px 10px;
    margin: 10px 5px;
    box-sizing: border-box;
}
input[type=password]{
  width: 60%;
    padding: 10px 10px;
    margin: 10px 5px;
    box-sizing: border-box;
}
input[type=email]{
  width: 60%;
    padding: 10px 10px;
    margin: 10px 5px;
    box-sizing: border-box;
}

select {
  border: 0 none;
  color: #FFFFFF;
  background: transparent;
  font-size: 18px;
  padding: 2px 8px;
  width: 80%;
  background: <?=$lightGray?>;
}

table {
margin: 8px;
}

h1 {
font-family: <?=$font_family?>;
font-size: 15;
color: $linkColor;
text-align: center;
}

h3 {
font-family: <?=$font_family?>;
font-size: 20;
color: $linkColor;
text-align: center;
}

h5 {
font-family: <?=$font_family?>;
font-size: 30;
color: <?=$grayText?>;
text-align: center;
}



?>

