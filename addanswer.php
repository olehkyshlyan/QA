<?if(isset($_SESSION['euser']) && $_SESSION['euser'] == true){if(!isset($_SESSION['asent']) && $_SESSION['asent'] != true){$_SESSION['asent'] = true;$aaerr = false;$_SESSION['aaerr'] = '';$addanswer = false;if(isset($_POST['addanswer'])){unset($_POST['addanswer']);$addanswer = true;if(isset($_POST['qnum'])){  $qnum = preg_replace('/[^0-9]/','',substr($_POST['qnum'],0,9));  unset($_POST['qnum']);  if(is_numeric($qnum)){	if($qnum != ''){	  $qid = $qnum;	}	else{	  $aaerr = true;	  $_SESSION['aaerr'] .= "Question 'id' is wrong<br />";	}  }  else{    $aaerr = true;	$_SESSION['aaerr'] .= "Question 'id' is not a number<br />";  }}else{  $aaerr = true;  $_SESSION['aaerr'] .= "Question 'id' is undefined<br />";}if(isset($_POST['qauthor'])){  $tqauth = preg_replace('/[^a-z0-9\_\-\+\=\&\.]/i','',substr($_POST['qauthor'],0,50));  unset($_POST['qauthor']);  if($tqauth != ''){    $qauth = $tqauth;  }  else{    $aaerr = true;    $_SESSION['aaerr'] .= "Author of the question is undefined<br />";  }}else{  $aaerr = true;  $_SESSION['aaerr'] .= "Author of the question is not set<br />";}if(isset($_SESSION['uid'])){  $uid = $_SESSION['uid'];}else{  $aaerr = true;  $_SESSION['aaerr'] .= "User's 'id' is undefined<br />";}if(isset($_SESSION['utype'])){  $utype = $_SESSION['utype'];  //print('$utype: '.$utype.'<br />');}else{  $aaerr = true;  $_SESSION['aaerr'] .= "User's type is undefined<br />";}if(isset($_SESSION['fname'])){  $fname = $_SESSION['fname'];  //print('$fname: '.$fname.'<br />');}else{  $aaerr = true;  $_SESSION['aaerr'] .= "User's first name is undefined<br />";}if(isset($_SESSION['lname'])){  $lname = $_SESSION['lname'];  //print('$lname: '.$lname.'<br />');}else{  $aaerr = true;  $_SESSION['aaerr'] .= "User's last name is undefined<br />";}// ANSWER TEXTif(isset($_POST['atext'])){  $at = mb_substr((string)$_POST['atext'],0,1001,'UTF-8');  unset($_POST['atext']);  if(is_string($at)){    $at = str_replace(array("\x0A\x0D","\x0D\x0A","\x0A","\x0D"),"\n",$at);    $lat = mb_strlen($at,'UTF-8');    if($lat <= 1000){	  if($at != ''){      preg_match_all('/[\\n]/u',$at,$atmatches,PREG_OFFSET_CAPTURE);      if(count($atmatches[0]) > 19){        $sppos = $atmatches[0][19][1];        $fpcont = mb_strcut($at,0,$sppos,'UTF-8');        $spcont = mb_strcut($at,$sppos);        $fspcont = str_replace("\n",'',$spcont);        $at = $fpcont.$fspcont;      }      $atext = addslashes($at);      $atext = htmlentities($atext,ENT_QUOTES,'UTF-8');      $_SESSION['atext'] = $at;	  }	  else{	    $aaerr = true;      $_SESSION['aaerr'] .= "Answer text is empty<br />";	  }	}	else{	  $aaerr = true;	  $_SESSION['aaerr'] .= "Answer text is longer than 1000 characters<br />";	  $_SESSION['atext'] = $at;	}  }  else{    $aaerr = true;    $_SESSION['aaerr'] .= "Answer text is not a string<br />";  }}else{  $aaerr = true;  $_SESSION['aaerr'] .= "Answer is undefined<br />";}// создание папки 'imgfolder' для постоянного хранения фото. Название папки - 'год + месяц'// присвоение переменной 'aimages' массива c названиями фото из элемента сессии 'auplphoto'// фото временно хранятся в папке временного хранения 'tmpimg' до момента успешного добавления ответа$aimages = '';$imgf = '';if(isset($_SESSION['auplphoto'])){  $imgf = gmdate('Ym');  if(!is_dir('images/'.$imgf)){    $crfolder = mkdir('images/'.$imgf);	if($crfolder == false){	  $aaerr = true;	  $_SESSION['aaerr'] .= "Folder for images was not created<br />";	}  }  if(is_dir('images/'.$imgf)){    $aimages = $_SESSION['auplphoto'];  }  else{    $aaerr = true;	$_SESSION['aaerr'] .= "Folder for images does not exist<br />";  }}// сканируется папка временного хранения фото-файлов для формирования соответствующего массива// из массива удалаются названия файлов, которые не имеют расширения '.jpg','.jpeg','.png','.gif'// например (в Windows XP): 'Thumbs.db' и файлы с точками (количество точек указывает на уровень вложенности папки)$tmpimages = scandir('tmpimg');if($tmpimages != false){foreach($tmpimages as $k=>$v){  $tiext = strrchr($v,'.');  if(!in_array($tiext,array('.jpg','.jpeg','.png','.gif'))){	unset($tmpimages[$k]);  }}$tmpimgnum = count($tmpimages);// при каждом добавлении вопроса из папки временного хранения фото-файлов удаляются фото-файлы, дата которых меньше текущей даты на 1 день// дата фото-файла определяется по первым восьми символам названия файла// название фото-файла формируется при загрузке файла функцией даты до уровня 'секунды' + случайное числоif($tmpimgnum > 0){$currdate = (int)gmdate('Ymd');foreach($tmpimages as $k=>$v){  $imgdate = strtotime(substr($v,0,8));  $imgdate = strtotime('+1 day',$imgdate);  $imgdate = (int)gmdate('Ymd',$imgdate);  if($imgdate < $currdate){	unlink('tmpimg/'.$v);  }}}}if(isset($_SESSION['usphoto'])){if($_SESSION['usphoto'] != ''){  $usphoto = basename($_SESSION['usphoto']);  $newfile = 'uphotos/'.$usphoto;  if(!file_exists($newfile)){  if(!copy('usphotos/'.$usphoto,$newfile)){    $aaerr = true;    $_SESSION['aaerr'] .= "User's photo was not copied<br />";  }  }}}}if($addanswer == true && $aaerr == false){$dt = gmdate('Y-m-d H:i:s');//print('$dt: '.$dt.'<br />');$complaint = 'no';$checked = 'no';try{$ainsert = $db->exec("INSERT INTO answers (qid,qauth,uid,utype,fname,lname,atext,imgf,aimages,uphoto,dt,complaint,checked) VALUES ($qid,'$qauth','$uid','$utype','$fname','$lname','$atext','$imgf','$aimages','$usphoto','$dt','$complaint','$checked')");//print('$ainsert: '); var_dump($ainsert); print('<br />');$db->exec("UPDATE users SET lrec='$dt' WHERE uid='$uid';");if($ainsert == 1){// копирование фото из папки временного хранения фото 'tmpimg' в папку 'images' и удаление фото из папки 'tmpimg'$aphotoarray = explode("|sp|",$_SESSION['auplphoto']);foreach($aphotoarray as $v){  copy('tmpimg/'.$v,'images/'.$imgf.'/'.$v);  unlink('tmpimg/'.$v);}$aquery = $db->query("SELECT qid FROM answers WHERE qid='$qid';");//print('$aquery: '); var_dump($aquery); print('<br />');$arows = $aquery->fetchAll(PDO::FETCH_ASSOC);//print('$arows: '); var_dump($arows); print('<br />');$arowsnum = count($arows);//print('$arowsnum: '.$arowsnum.'<br />');$qupdate = $db->exec("UPDATE questions SET answers = '$arowsnum' WHERE id='$qid';");//print('$qupdate: '); var_dump($qupdate); print('<br />');}else{  $_SESSION['aaerr'] .= "Adding the answer failed<br />";}}catch(Exception $e){  $_SESSION['aaerr'] .= $e->getMessage();}if($_SESSION['aaerr'] == ''){  unset($_SESSION['atext']);  unset($_SESSION['qnum']);  unset($_SESSION['qauthor']);  unset($_SESSION['aaerr']);  unset($_SESSION['auplphoto']);}}header('Location:http://'.$currenturl); exit();}}?>