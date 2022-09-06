<?php
// header("HTTP/1.1 200 OK");

// if ( isset( $_SERVER["REQUEST_URI"] ) ) {        $arr = explode( "/", $_SERVER["REQUEST_URI"] );

//     foreach ( $arr as $chave => $valor ) {        if ( empty( $valor ) ) { unset( $arr[$chave] ); }}

//     $arr = count( $arr ) ? array_merge( $arr ) : null;

//     $arquivo = isset( $arr[0] ) && file_exists( $arr[0].".php" ) ? $arr[0] : "index.php";
//     $artigo = isset( $arr[1] ) ? $arr[1] : 1;
//     $pagina = isset( $arr[2] ) ? $arr[2] : 1;
// }


$headers = apache_request_headers();

$headers["Content-Type"];
$headers["Authorization"];

$Content = "application/json";
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJSZXNwb25zYXZlbCIsInVzdWFyaW8iOiJ7XCJlbWFpbFwiOlwic3Vwb3J0ZWZvcnRoQGdtYWlsLmNvbVwiLFwiY29kcHJvZlwiOjAsXCJiYW5jb2RhZG9zXCI6XCJyZWlub1wiLFwibm9tZXByb2Zlc3NvclwiOlwiQURNSU5JU1RSQURPUlwiLFwidGlwb1wiOlwiQUNcIixcImNvZGlnb2ludGVybm9cIjowLFwibHN0YWx1bm9cIjpbXX0ifQ.1GmbJL57apu5PwZcAoy4TBjR1zC8sZuK3h8ZdS-QcSM";



if($headers["Content-Type"] == $Content){echo "type correto";}
if($headers["Authorization"] == $token){echo "token certo";}



?>