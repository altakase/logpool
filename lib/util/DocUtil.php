<?php
namespace lib\util;

require_once( dirname( __FILE__ ) . '/../Constants.php' );
require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

class DocUtil{
  public static function outputPDFFromTextile($source) {
    $uuid = uniqid();
    $parser = new \Netcarver\Textile\Parser();
    $output = $parser->textileThis($source);

    $output = mb_ereg_replace('<table>', '<table class="table table-bordered">', $output);
    $output = <<<EOT
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="../web/css/bootstrap.min.css">
    <title></title>
</head>
<body>
<div class="col-md-9">
$output
</div>
</body>
</html>
EOT;
    
    $output_path = WORK_DIR . '/' .$uuid. '.html';
    file_put_contents($output_path, $output);

    $output_pdf_path = WORK_DIR . '/' .$uuid. '.pdf';
    shell_exec("/usr/local/bin/wkhtmltopdf $output_path $output_pdf_path");
    
    return $output_pdf_path;
  }

  public static function outputPasswordZip($source_path, $password) {
    $output_zip_path =  preg_replace("/(.+)(\.[^.]+$)/", "$1.zip", $source_path);
    shell_exec("zip -jP $password $output_zip_path $source_path");

    return $output_zip_path;
  }
}
