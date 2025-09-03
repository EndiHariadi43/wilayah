--- a/apps/inc/geo_ajax.php
+++ b/apps/inc/geo_ajax.php
@@
 <?php
 /*
 BISMILLAAHIRRAHMAANIRRAHIIM - In the Name of Allah, Most Gracious, Most Merciful
 ================================================================================
 filename : geo_ajax.php
 purpose  :
 create   : 170912
-<<<<<<< HEAD
-last edit: 2024-12-19 10:31:03
-=======
-last edit: 2024-12-19 10:35:03
->>>>>>> 7de39290e2d601d2468ca5dd0a6a65abfa0a85f2
+last edit: 2024-12-19 10:35:03
 author   : cahya dsn
 ================================================================================
 This program is free software; you can redistribute it and/or modify it under the
 terms of the MIT License.
@@
 copyright (c) 2017-2024 by cahya dsn; cahyadsn@gmail.com
 ================================================================================*/
 include "db.php";
-$r=array('status'=>false,'error'=>'an error occured');
-if (!empty($_GET['id'])){
+header('Content-Type: application/json; charset=utf-8');
+header('Cache-Control: public, max-age=3600');
+
+$r = ['status'=>false,'error'=>'an error occured'];
+
+if (!empty($_GET['id'])) {
+  $id = (string)$_GET['id'];
+  // validasi: hanya digit, dan panjang kode wilayah umum (2/5/8/13)
+  $len = strlen($id);
+  $allowedLen = [2,5,8,13];
+  if (!ctype_digit($id) || !in_array($len, $allowedLen, true)) {
+    echo json_encode(['status'=>false,'error'=>'invalid id']);
+    exit;
+  }
+
   $query = $db->prepare("SELECT * FROM {$tbl_wilayah} WHERE kode=:id");
-  $query->execute(array(':id'=>$_GET['id']));
+  $query->execute([':id'=>$id]);
   $d = $query->fetchObject();
-  if(empty($d->lat)){
-    $r=array('status'=>false,'error'=>'data not found');
-  }else{
-    $path=$d->path;
-    if(empty($path)){
-      $path='[['
-            .($d->lat-0.01).','.($d->lng-0.01).'],['
-            .($d->lat+0.01).','.($d->lng-0.01).'],['
-            .($d->lat+0.01).','.($d->lng+0.01).'],['
-            .($d->lat-0.01).','.($d->lng+0.01).']]';
-    }
-    $data=array('kode'=> $d->kode,'nama'=> $d->nama,'lat'=> $d->lat,'lng'=> $d->lng,'path'=>$path,'luas'=>$d->luas,'penduduk'=>$d->penduduk);
-    $r=array('status'=>true,'data'=>$data);
-  }
-  if(empty($_GET['geo'])){
-    $n=strlen($_GET['id']);
-    $m=($n==2?5:($n==5?8:13));
-    $wil=($n==2?'Kota/Kab':($n==5?'Kecamatan':'Desa/Kelurahan'));
-    $query = $db->prepare("SELECT * FROM {$tbl_wilayah} WHERE LEFT(kode,:n)=:id AND CHAR_LENGTH(kode)=:m ORDER BY nama");
-    $query->execute(array(':n'=>$n,':id'=>$_GET['id'],':m'=>$m));
-    $opt="<option value=''>Pilih {$wil}</option>";
-    while($d = $query->fetchObject()){
-        $opt.="<option value='{$d->kode}'>{$d->nama}</option>";
-    }
-    $r['opt']=$opt;
-    $r['n']=$n;
-  }
+  if (empty($d) || empty($d->lat)) {
+    $r = ['status'=>false,'error'=>'data not found'];
+  } else {
+    $path = $d->path;
+    if (empty($path)) {
+      $path = '[['
+            .($d->lat-0.01).','.($d->lng-0.01).'],['
+            .($d->lat+0.01).','.($d->lng-0.01).'],['
+            .($d->lat+0.01).','.($d->lng+0.01).'],['
+            .($d->lat-0.01).','.($d->lng+0.01).']]';
+    }
+    $data = [
+      'kode'      => $d->kode,
+      'nama'      => $d->nama,
+      'lat'       => $d->lat,
+      'lng'       => $d->lng,
+      'path'      => $path,
+      'luas'      => $d->luas,
+      'penduduk'  => $d->penduduk
+    ];
+    $r = ['status'=>true,'data'=>$data];
+  }
+
+  // jika bukan request peta murni (geo=1), sertakan opsi anak wilayah
+  if (empty($_GET['geo'])) {
+    $m   = ($len===2 ? 5 : ($len===5 ? 8 : 13));
+    $wil = ($len===2 ? 'Kota/Kab' : ($len===5 ? 'Kecamatan' : 'Desa/Kelurahan'));
+    $q2 = $db->prepare("SELECT * FROM {$tbl_wilayah} WHERE LEFT(kode,:n)=:id AND CHAR_LENGTH(kode)=:m ORDER BY nama");
+    $q2->execute([':n'=>$len, ':id'=>$id, ':m'=>$m]);
+    $opt = "<option value=''>Pilih {$wil}</option>";
+    while ($row = $q2->fetchObject()) {
+      $kode = htmlspecialchars($row->kode, ENT_QUOTES, 'UTF-8');
+      $nama = htmlspecialchars($row->nama, ENT_QUOTES, 'UTF-8');
+      $opt .= "<option value='{$kode}'>{$nama}</option>";
+    }
+    $r['opt'] = $opt;
+    $r['n']   = $len;
+  }
 }
-echo json_encode($r);
+echo json_encode($r, JSON_UNESCAPED_UNICODE);

