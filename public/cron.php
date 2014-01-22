<?
chdir(dirname(__FILE__));
require './../vendor/autoload.php';
require 'cron.config.php';
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum aus Vergangenheit
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($db->rsslist->where('last_update<UNIX_TIMESTAMP()-refresh_time AND refresh_time>0')->count()>0){
	$links=$db->rsslist->where('last_update<UNIX_TIMESTAMP()-refresh_time AND refresh_time>0');
	foreach ($links as $link) {

		$headers=array('Accept' => 'text/xml');
		if(strlen($link['cookie'])>0) {
			$headers['Cookie']=$link['cookie'];
		}
		$answ=Requests::get($link['url'], $headers);

		$data=preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $answ->body);


		if(simplexml_load_string($data)) {
			preg_match('/^\<\?xml.*?encoding\s*\=\s*"(.*?)".*\?\>/', $data,$m);
			if($m && strtolower($m[1])!='utf-8') {
				$data=iconv($m[1], 'utf-8', str_replace($m[1], 'utf-8', $data));
			}
			// var_dump($m);
			// $attr=$xml->attributes();
			// var_dump($answ->headers);
			// highlight_string(prin,true));

			// break;
			
			$db->rss_log->insert(array(
					'rsslist_id'=>$link['id'],
					'time'=>time(),
					'xml_string'=>$data
				));
			$db->rss_log->where('time<'.(time()-604800))->delete();
			$link->update(array('last_update'=>time()));
//* /
			// $xml=new SimpleXMLElement($answ->body);xmlEndoding
			// highlight_string($xml->channel->item[0]->asXML());
		} else {
			$h=fopen('xml.xml', 'w');
			fwrite($h,$answ->body);
			fclose($h);
		}
	}	
}

// echo '<pre>';
if($db->rss_log->where('parsed=0')->count()>0){
	$xmls=$db->rss_log->where('parsed=0');
	foreach ($xmls as $xml_row) {
		// highlight_string($xml_row['xml_string']);
		// echo strlen($xml_row['xml_string']);
		$xml=new SimpleXMLElement($xml_row['xml_string']);
		// var_dump($xml);
		$items=$xml->xpath('//item');
		// var_dump($items);
		$enc=$xml->xpath('//enclosure[@type="application/x-bittorrent"]');
		// var_dump($enc);
		$parsed=0;
		foreach ($items as $key=>$item) {
			$item_data=array(
				'title'=>(string)$item->title,
				'description'=>(string)$item->description,
				'date'=>strtotime($item->pubDate),
				);
			if($enc && count($enc)>0) {
				// highlight_string($item->asXML());

				// $enc=$item->xpath('//enclosure[@type="application/x-bittorrent"]');
				// var_dump($enc[$key]);
				$enc_attr=$enc[$key]->attributes();
				// var_dump($enc_attr['url']);
				$item_data['link']=(string)$enc_attr['url'];
			}elseif(isset($item->link)) {
				$item_data['link']=(string)$item->link;
			} else {
				continue;
			}
			// highlight_string(print_r($item_data));
			if($db->rss_items->where(array('rsslist_id'=>$xml_row['rsslist_id'],'link'=>$item_data['link']))->count()==0) {
				$db->rss_items->insert(array(
						'title'=>$item_data['title'],
						'description'=>$item_data['description'],
						'item_time'=>$item_data['date'],
						'link'=>$item_data['link'],
						'added'=>time(),
						'rsslist_id'=>$xml_row['rsslist_id'],
						'rss_log_id'=>$xml_row['id'],
						'raw'=>$item->asXML(),
					));
			}
			$parsed++;

			// var_dump($item_data);
		}
		if($parsed>0) {
			$xml_row->update(array('parsed'=>$parsed));
		}

		// var_dump($items);

	}	
}
//echo '</pre>';
$send=array();
if($db->filters->where('active=1')->count()>0){
	$query=array();
	foreach ($db->filters->where('active=1') as $filter) {
		$subQuery=array('rsslist_id='.$filter['rsslist_id']);
		// $subQuery[]=
		if(strlen($filter['include'])>0) {
		    $inc=explode(', ',$filter['include']);
		    foreach ($inc as $value) {
		        $value=trim($value);
		        if(strlen($value)>0) {
		            $subQuery[]='title REGEXP "'.addcslashes($value, "\n\r\"").'"';
		        }

		    }
		}
		if(strlen($filter['exclude'])>0) {
		    $inc=explode(', ',$filter['exclude']);
		    foreach ($inc as $value) {
		        $value=trim($value);
		        if(strlen($value)>0) {
		            $subQuery[]='title NOT REGEXP "'.addcslashes($value, "\n\r\"").'"';
		        }
		    }
		}
		$query[]='('.implode(' AND ',$subQuery).')';
	}
	$files=$db->rss_items->where('file_name="" AND ('.implode(' OR ',$query).')');
	// echo (string)$files;
	foreach ($files as $file) {
		// highlight_string(print_r($firstFile,true));
		$rss=$file->rsslist;
		$cookie=$rss['cookie'];

		$headers=array('Accept' => 'application/x-bittorrent');
		if(strlen($cookie)>0) {
			$headers['Cookie']=$cookie;
		}
		$answ=Requests::get($file['link'], $headers);
		$filename=$answ->headers['Content-Disposition'];//: attachment; filename="Mortal.Kombat.Legacy.S01E04.rus.LostFilm.TV.torrent"; ']
		preg_match('/filename="([^"]*)";?/',$filename,$m);
		// print $m[1].'<br>';
		// highlight_string(print_r($m,true));
		// highlight_string($answ->raw);
		$file->update(array(
				'file_name'=>$m[1],
				'file'=>$answ->body,
			));
		$send[]=array(
				'rss'=>$rss['name'],
				'title'=>$file['title'],
				'date'=>$file['item_time'],
				'file'=>$file['file_name'],
				'id'=>$file['id'],
			);
	}
	foreach ($db->filters->where('active=1') as $filter) {
		$query=array('rsslist_id='.$filter['rsslist_id']);
		// $subQuery[]=
		if(strlen($filter['include'])>0) {
		    $inc=explode(', ',$filter['include']);
		    foreach ($inc as $value) {
		        $value=trim($value);
		        if(strlen($value)>0) {
		            $query[]='title REGEXP "'.addcslashes($value, "\n\r\"").'"';
		        }

		    }
		}
		if(strlen($filter['exclude'])>0) {
		    $inc=explode(', ',$filter['exclude']);
		    foreach ($inc as $value) {
		        $value=trim($value);
		        if(strlen($value)>0) {
		            $query[]='title NOT REGEXP "'.addcslashes($value, "\n\r\"").'"';
		        }
		    }
		}
		$rows=$db->rss_items->where('file_name!="" AND filters_id=0 AND '.implode(' AND ',$query).'');
		// echo (string)$rows.'<br/>';
		$rows->update(array('filters_id'=>$filter['id']));
		
	}
}
check_groups($db);
$notgroped=$db->rss_items->where('groups_id=0');
if(!empty($send) || $notgroped->count()>0) {
	$subject=array();
	$text='';
	if(!empty($send)) {
		$subject[]='Новые подписки';
		$text.='<h3>Обновления RSS</h3><br/><br/>';
		foreach ($send as $key => $value) {
			$text.=''.$value['rss'].' \ <b>'.$value['title'].'</b> <small>('.date('d/m H:i',$value['date']).')</small> <a href="http://rss.myexg.ru/t/'.$value['id'].'">.t</a><br/>';
		}
	}

	if($notgroped->count()>0) {
		$subject[]='Новые сериалы';
		$text.='<h3>Новые сериалы в ленте</h3><br/><br/>';
		foreach ($notgroped as $key => $value) {
			$text.=''.$value->rsslist['name'].' \ <b>'.$value['title'].'</b> <small>('.date('d/m H:i',$value['item_time']).')</small><br/>';
		}
		$notgroped->update(array('groups_id'=>-1));
	}

	$mail = new PHPMailer;

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = $config['email.server'];  // Specify main and backup server
	$mail->Port = $config['email.port'];  // Specify main and backup server
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $config['email.user'];                            // SMTP username
	$mail->Password = $config['email.pass'];                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
	$mail->CharSet = 'UTF-8';
	$mail->From = $config['email.from'];
	$mail->FromName = 'MyRSS';
	$mail->addAddress($config['email']);  // Add a recipient
	// $mail->addAddress('ellen@example.com');               // Name is optional
	// $mail->addReplyTo('info@example.com', 'Information');
	// $mail->addCC('cc@example.com');
	// $mail->addBCC('bcc@example.com');

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'MyRSS: '.implode(' / ',$subject);
	$mail->Body    = $text;
	// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
	   // echo 'Message could not be sent.';
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	   // exit;
	}

	// mail();
}
//*/
// libxml_use_internal_errors(true);
// $data=file_get_contents('xml.xml');
// $data=$db->rss_log[1]['xml_string'];
// $data = preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;', $data);
// $data = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $data);
// $xml=new SimpleXMLElement($data);
// var_dump($xml);
// var_dump($xml->channel->item[0]);
// highlight_string($xml->channel->item[0]->asXML());
// highlight_string($db->rss_log[1]['xml_string']);
//MEDIUMBLOB
// count($links);
// if(count)
/*
$answ=Requests::get($link['url'], array('Accept' => 'text/xml','Cookie'=>$link['cookie']));
$h=fopen('xml.xml', 'w');
fwrite($h,$answ->body);
fclose($h);

$log->info(var_export($answ,true));
*/
//headers
/*

object(Requests_Response_Headers)[19]
  protected 'data' => 
    array
      'server' => 
        array
          0 => string 'nginx' (length=5)
      'date' => 
        array
          0 => string 'Sat, 21 Dec 2013 00:02:34 GMT' (length=29)
      'content-type' => 
        array
          0 => string 'application/x-bittorrent' (length=24)
      'keep-alive' => 
        array
          0 => string 'timeout=10' (length=10)
      'x-powered-by' => 
        array
          0 => string 'PHP/5.3.27' (length=10)
      'content-disposition' => 
        array
          0 => string 'attachment; filename="Person.of.Interest.S03E11.720p.WEB.rus.LostFilm.TV.torrent";' (length=82)

$data=file_get_contents('xml.xml');

$xml=new SimpleXMLElement($data);
// var_dump($xml);
// var_dump($xml->channel->item[0]);
highlight_string($xml->channel->item[0]->asXML());
/*
$answ=Requests::get($xml->channel->item[0]->link, array('Cookie'=>$link['cookie']));
$h=fopen('xml.torrent', 'w');
fwrite($h,$answ->body);
fclose($h);

var_dump($answ->headers);
$log->info(var_export($answ->headers,true));
?>
*/