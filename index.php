<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <title>XVIDEOS-DOWNLOADER</title>
 <link rel="stylesheet" href="./style.css">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <style>
  #progress-container {
display: none;
width: 65%;
margin-top: 20px;
 border-radius: 6px;
  }
  #progress-bar {
width: 0%;
height: 10px;
background-color: white;
border-radius: 6px;
  }
  #progress-percentage {
display: none;
margin-top: 10px;
color: white;
  }
 </style>
</head>
<body>
 <center>
  <div style="width: 60%;">
  <img src="xvideos.png" style="width: 35%;height:55px;margin-top: 20px;">
  <form action="" method="post">
<input class="url" type="text" name="url" autocomplete="off" required>
<p>
 <div id="progress-container">
<div id="progress-bar"></div>
  </div>
  <div id="progress-percentage">0%</div>
  <p>
<input class="btn" type="submit" value="DESCARGAR">
  </form>

  
</p>
  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
$url = htmlspecialchars($_POST['url']);
$ch = curl_init();
$header = array('Cache-Control: max-age=0');
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$page = curl_exec($ch);
curl_close($ch);
$file = fopen("info.txt", "w");
fwrite($file, '');
$file = fopen("info.txt", "a");
fwrite($file, $page);
$filename = 'info.txt';
$searchWord = 'contentUrl';
$titleTag = '<title>';
$titulo = '';
$handle = fopen($filename, 'r');
if ($handle) {
 while (($line = fgets($handle)) !== false) {
  if (strpos($line, $searchWord) !== false) {
$palabras_a_reemplazar = array('"contentUrl": "', '",');
$reemplazos = array("", "");
$link = str_replace($palabras_a_reemplazar, $reemplazos, $line);
break;
  }
  if (strpos($line, $titleTag) !== false) {
preg_match('/<title>(.*?)<\/title>/', $line, $matches);
if (isset($matches[1])) {
 $titulo = $matches[1]; 
}
  }
 }
 fclose($handle);
 if ($titulo !== '') {
  $title = str_replace(' - XVIDEOS.COM', '.mp4', $titulo);
  $titulo = json_encode($title);
  $enlace = json_encode($link);
  $javascriptCode = "<script>
  fetch($enlace)
  .then(response => {
if (!response.ok) {
 throw new Error('Network response was not ok');
}
const contentLength = response.headers.get('Content-Length');
const total = parseInt(contentLength, 10);
let loaded = 0;
const reader = response.body.getReader();
const chunks = [];
function push() {
 return reader.read().then(({ done, value }) => {
  if (done) {
return;
  }
  chunks.push(value);
  loaded += value.length;
  const percent = (loaded / total) * 100;
  document.getElementById('progress-bar').style.width = percent + '%';
  document.getElementById('progress-percentage').innerText = Math.round(percent) + '%';
  document.getElementById('progress-percentage').style.display = 'block';
  document.getElementById('progress-container').style.display = 'block';
  return push();
 });
}
return push().then(() => {
 return new Blob(chunks);
});
  })
  .then(blob => {
const a = document.createElement('a');
a.href = URL.createObjectURL(blob);
a.download = '$titulo';
document.body.appendChild(a);
a.click();
document.body.removeChild(a);
URL.revokeObjectURL(a.href);
  })
  .catch(error => {
console.error('Hubo un problema con la descarga:', error);
  });
  </script>";
  $descargar=str_replace('\\', '', $javascriptCode);
  echo "$descargar";

 } else {
  echo "No se encontró el título.<br>";
 }
} else {
 echo "Error al abrir el archivo.";
}
  }
  ?>
 </center>
</body>
</html>
