<?php
//data記録用ファイル
define("DATA", './data.csv');
/**
 * POST:リクエストデータをCSVに記録
 * GET：CSVを読み込みJSONで出力
 */
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //formからの値を取得する
    $favorite = $_POST['favorite'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    //送信データの配列化
    $array = array($favorite,$age,$gender);
    //ファイルを開く
    $fp = fopen(DATA, 'a');
    //ファイルに排他ロックをかける
    if(flock($fp, LOCK_EX)){
        //csvに書き出す
        fputcsv($fp, $array);
        //ロックを解除
        flock($fp, LOCK_UN);
    }
    //ファイルを閉じる
    fclose($fp);
    //結果ページにリダイレクト
    header('location:./result.html');
    exit();
}else{
    //ファイルの存在を調べる
    if(!file_exists(DATA)){
        $json_array = array(
            'error'=>'データが未登録です',
        );
    }else{
        $json_array = array();
        //CSVを読み込み
        $fp = fopen(DATA, 'r');
        while($line = fgetcsv($fp)){
            $array = array(
                'favorite' => $line[0],
                'age' => $line[1],
                'gender' => $line[2],
            );
            array_push($json_array,$array);
        }
        fclose($fp);
    }
    //連想配列をJSONに変換
    $json = json_encode($json_array);
    //HTTPレスポンスをJSONに変更
    header("Content-Type: text/javascript; charset=utf-8");
    echo $json;
    exit();
}   