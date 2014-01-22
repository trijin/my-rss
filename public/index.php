<?php
require '../vendor/autoload.php';
require 'config.php';

// header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Datum aus Vergangenheit
// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
// header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    // $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    // $app->view()->appendData( array( 'data2' => 'Привет Slim2' ) );
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->view()->appendData( $data );
    $app->render('main.tpl');
});

$app->post('/list', function () use ($app) {
    if(is_numeric($app->request->post('editrssid')) && (int)$app->request->post('editrssid')>0) {
        if(!is_null($app->request->post('delete')) && $app->request->post('delete')=='DELETE') {
            $app->db->rsslist[$app->request->post('editrssid')]->delete();
        } else {
            $app->db->rsslist[$app->request->post('editrssid')]->update(array(
                'name'=>$app->request->post('name'),
                'url'=>$app->request->post('url'),
                'cookie'=>$app->request->post('cookie'),
                ));
        }
    } else {
        $app->db->rsslist()->insert(array(
            'name'=>$app->request->post('name'),
            'url'=>$app->request->post('url'),
            'cookie'=>$app->request->post('cookie'),
            ));
    }
    $app->redirect($app->request->getPath());
});

$app->get('/list', function () use ($app) {
    $data['links']=array();
    foreach ($app->db->rsslist() as $row) {
        $data['links'][]=$row;
    }
    // $data['links']=$app->request->getRootUri().'/';
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('rsslist.html');
});
function getFilterList($params) {
    global $app;


}
$app->post('/filterpreview', function () use ($app) {
    $query=array();
    $p=$app->request->post();
    if(isset($p['rsslist_id'])) {
        $query[]='rsslist_id='.$p['rsslist_id'];
    }
    if(isset($p['include']) && !empty($p['include'])) {
        $inc=explode(', ',$p['include']);
        foreach ($inc as $value) {
            $value=trim($value);
            if(strlen($value)>0) {
                $query[]='title REGEXP "'.addcslashes($value, "\n\r\"").'"';
            }

        }
    }
    if(isset($p['exclude']) && !empty($p['exclude'])) {
        $inc=explode(', ',$p['exclude']);
        foreach ($inc as $value) {
            $value=trim($value);
            if(strlen($value)>0) {
                $query[]='title NOT REGEXP "'.addcslashes($value, "\n\r\"").'"';
            }

        }
    }
    // echo implode(' AND ',$query);
    $filter=$app->db->rss_items->where(implode(' AND ',$query))->limit(15)->order("item_time DESC");
    // echo (string)$filter;
    $data=array();
    foreach ($filter as $row) {
        $r=array();
        //(array)$row;
        foreach ($row as $key => $value) {
            $r[$key]=$value;
        }

        $r['rss_name']=$row->rsslist['name'];
        $data['items'][]=$r;
    }
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('filterlist.tpl');

    // var_dump($app->request->post());
});
$app->post('/filter', function () use ($app) {
// $app->post('/list', function () use ($app) {
    if(is_numeric($app->request->post('editfilterid')) && (int)$app->request->post('editfilterid')>0) {
        if(!is_null($app->request->post('delete')) && $app->request->post('delete')=='DELETE') {
            $app->db->filters[$app->request->post('editfilterid')]->delete();
            $app->db->rss_items->where('filters_id='.$app->request->post('editfilterid'))->update(array('filters_id'=>0));
        } else {
            $app->db->filters[$app->request->post('editfilterid')]->update(array(
                'name'=>$app->request->post('name'),
                'tag'=>$app->request->post('tag'),
                'include'=>$app->request->post('include'),
                'exclude'=>$app->request->post('exclude'),
                // 'dirname'=>$app->request->post('dirname'),
                'rsslist_id'=>$app->request->post('rsslist_id'),
                ));
                $app->flash('app', 'update');
        }
    } else {
        $app->db->filters()->insert(array(
            'include'=>$app->request->post('include'),
            'tag'=>$app->request->post('tag'),
            'name'=>$app->request->post('name'),
            'exclude'=>$app->request->post('exclude'),
            // 'dirname'=>$app->request->post('dirname'),
            'rsslist_id'=>$app->request->post('rsslist_id'),
            ));
            $app->flash('app', 'insert');
    }
    $app->redirect($app->request->getPath());
});
$app->get('/filter', function () use ($app) {
    foreach ($app->db->rsslist() as $row) {
        $data['rsss'][]=array(
            'id'=>$row['id'],
            'name'=>$row['name'],
        );//$row;
    }
    // $app->flash('app', 'test');
    foreach ($app->db->filters()->where('active=1')->order('name') as $row) {
        $r=array();//(array)$row;
        foreach ($row as $key => $val) {
            $r[$key]=$val;
        }
        $r2=$row->rsslist;
        $r['rss_name']=$r2['name'];
        $data['filterss'][$r2['name']]['list'][]=$r;
        $data['filterss'][$r2['name']]['name']=$r2['name'];
        $data['filterss'][$r2['name']]['id']=$r2['id'];
    }
    ksort($data['filterss']);
    /*usort($data['filterss'],function($a,$b) {
        return ($a['rss_name']>$b['rss_name']?1:-1);
    });*/
    // var_dump($data);
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('filters.tpl');
});
$app->post('/groups', function () use ($app) {
// $app->post('/list', function () use ($app) {
    if(is_numeric($app->request->post('editgroupid')) && (int)$app->request->post('editgroupid')>0) {
        $app->db->rss_items->where('groups_id='.$app->request->post('editgroupid'))->update(array('groups_id'=>'0'));
        if(!is_null($app->request->post('delete')) && $app->request->post('delete')=='DELETE') {
            $app->db->groups[$app->request->post('editgroupid')]->delete();
        } else {
            $app->db->groups[$app->request->post('editgroupid')]->update(array(
                'name'=>$app->request->post('name'),
                'ru_name'=>$app->request->post('ru_name'),
                'tag'=>$app->request->post('tag'),
                'include'=>$app->request->post('include'),
                'exclude'=>$app->request->post('exclude'),
                ));
            $app->flash('app', 'update');
        }
    } else {
        $app->db->groups()->insert(array(
            'include'=>$app->request->post('include'),
            'tag'=>$app->request->post('tag'),
            'name'=>$app->request->post('name'),
            'ru_name'=>$app->request->post('ru_name'),
            'exclude'=>$app->request->post('exclude'),
            ));
            $app->flash('app', 'insert');
    }
    check_groups($app->db);
    $app->redirect($app->request->getPath());
});
$app->get('/groups', function () use ($app) {
    $perPage=20;
    // echo (string)$app->db->groups()->where('active=1');
    foreach ($app->db->groups()->where('active=1')->order('name') as $row) {
        $r=array();//(array)$row;
        foreach ($row as $key => $val) {
            $r[$key]=$val;
        }
        // highlight_string(print_r($r,true));
        $data['groups'][]=$r;
    }
    $items=$app->db->rss_items->where("groups_id IN (0,-1)")->order("item_time")->limit($perPage);
    // $data=array();
    foreach ($items as $item) {
        $r=array();//(array)$row;
        foreach ($item as $key => $val) {
            $r[$key]=$val;
        }
        unset($r['description']);//=html_entity_decode($r['description'], 0 ,'UTF-8');
        unset($r['raw']);//=html_entity_decode($r['description'], 0 ,'UTF-8');
        unset($r['file']);//=html_entity_decode($r['description'], 0 ,'UTF-8');
        // $r['description']=htmlspecialchars_decode($r['description']);
        $data['items'][]=$r;
    }
    // var_dump($data);
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    // highlight_string(print_r($data,true));
    $app->view()->appendData( $data );
    $app->render('groups.tpl');
});
$app->get('/t/:id', function($id) use ($app){
    $file=$app->db->rss_items[$id];
    if($file && strlen($file['file_name'])>0) {
        header('Content-Type: application/x-bittorrent');
        header('Content-Disposition: attachment; filename="'.$file['file_name'].'";');
        /*if(strlen($file->filters['dirname'])) {
            require_once('../vendor/trijin/torrentEdit/torrent.php');
            $torrent=new torrent($file['file']);

            if($torrent->is_single_file()) {
                $torrent->array['info_hash']=strtoupper(sha1($torrent->code($torrent->array['info'])));
                $torrent->array['info']['files']=array(array('length'=>$torrent->array['info']['length'],'path'=>array($torrent->array['info']['name'])));
                unset($torrent->array['info']['length']);
                $torrent->array['info']['name']=$file->filters['dirname'];
                print $torrent->save();
            } else {
                print $file['file'];    
            }

        } else*/ {
            print $file['file'];
        }
    } else {
        $app->redirect($app->request->getRootUri().'/');
    }
    
});
$app->get('/getrss/all',function() use ($app) {
    // header('Content-type: application/rss+xml');
    $app->response->headers->set('Content-type','application/rss+xml');
    header('Pragma: public');
    header('Cache-control: private');
    header('Expires: -1');
    // echo "";
    $data['mainUrl']=$app->request->getUrl().$app->request->getRootUri().'/';
    // $data['mainUrl']=$app->request->getRootUri().'/';
    $list=$app->db->rss_items->where('file_name!=""')->order('item_time DESC')->limit(30);
    $data['maxDate']=$list->max('item_time');
    $data['items']=array();
    foreach ($list as $item) {
        $r=array();//(array)$row;
        foreach ($item as $key => $val) {
            $r[$key]=$val;
        }
        $flt=$app->db->filters[$item['filters_id']];
        // echo (string)$flt;
        $r['date']=date('Y-m-d H:i:s',$r['item_time']);
        if($flt){
            $r['tag']=$flt['tag'];
            $r['cat_name']=$flt['name'];
        }
        if($flt['id']>0) {
            $data['items'][]=$r;
        }
    }
    // $data['items']=array();
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('xml.tpl');
});
$app->get('/getrss/:id',function($id) use ($app) {
    $app->response->headers->set('Content-type','application/rss+xml');
    header('Pragma: public');
    header('Cache-control: private');
    header('Expires: -1');
    // echo "";
    $filter=$app->db->filters[$id]->fetch();
    // var_dump($filter);

    $data['mainUrl']=$app->request->getUrl().$app->request->getRootUri().'/';
    
    // $data['mainUrl']=$app->request->getRootUri().'/';
    $list=$app->db->rss_items->where('filters_id='.$id)->order('item_time DESC')->limit(30);

    $data['maxDate']=$list->max('item_time');
    $data['ListName']=$filter['name'];
    $data['items']=array();

    foreach ($list as $item) {
        $r=array();//(array)$row;
        foreach ($item as $key => $val) {
            $r[$key]=$val;
        }
        // $flt=$app->db->filters[$item['filters_id']];
        // echo (string)$flt;
        $r['date']=date('Y-m-d H:i:s',$r['item_time']);
        if($filter['tag']){
            $r['tag']=$filter['tag'];
        }
        $data['items'][]=$r;
    }
    // $data['items']=array();
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('xml.tpl');
})->conditions(array('id' => '\d+'));

$app->get('/last(_ung)(/:page)',function($page=1) use ($app) {
    $perPage=45;
    $rss=$app->db->rsslist();
    $groups=$app->db->groups();
    $filters=$app->db->filters->where('active=1');
    $items=$app->db->rss_items->order("item_time DESC")->limit($perPage,$perPage*($page-1));
    // echo $app->request->getResourceUri();
    if(strpos($app->request->getResourceUri(),'_ung')!==false) {
        $items->where(array('groups_id'=>array(0,-1)));
    }
    $data=array();
    foreach ($items as $item) {
        if(isset($data['items']) && count($data['items'])>0) {
            $ind=count($data['items'])-1;
            $last=$data['items'][$ind];
        } else {
            $ind=-1;
            $last=array();
        }
        
        $updatelast=false;
        if($ind>=0 ) {
            
        }
        $r=array();//(array)$row;
        foreach ($item as $key => $val) {
            $r[$key]=$val;
        }
        if($r['filters_id']>0 && $filters[$r['filters_id']]) {
            $r['times'][0]['filter_name']=$filters[$r['filters_id']]['name'];
        }
        if($r['groups_id']>0 && $groups[$r['groups_id']]) {
            $r['group']=array(
                'name'=>$groups[$r['groups_id']]['name'],
                'ru_name'=>$groups[$r['groups_id']]['ru_name'],
                );
            if(isset($last['groups_id']) && $r['groups_id']==$last['groups_id']) {
                $updatelast=true;
            }
        }
        
        $r['times'][0]['time_ago']=ago($r['item_time']);
        $r['times'][0]['name']=$r['title'];
        $r['times'][0]['rss_name']=$rss[$r['rsslist_id']]['name'];
        // $r['times'][0]['rss_name']=$rss[$r['rsslist_id']]['name'];
        $r['description']=html_entity_decode($r['description'], 0 ,'UTF-8');
        // $r['description']=htmlspecialchars_decode($r['description']);
        if($updatelast) {
            $data['items'][$ind]['times']=array_merge($data['items'][$ind]['times'],$r['times']);
        } else {
            $data['items'][]=$r;
        }
    }
    // highlight_string(print_r($data,true));
    $maxPage=ceil(($app->db->rss_items->order("item_time DESC")->count())/$perPage);
    // echo $maxPage;
    $dopusk=3;
    $needDots=true;
    for($i=1;$i<=$maxPage;$i++) {
        if(abs($page-$i)<$dopusk || $i<$dopusk || $maxPage-$i<$dopusk) {
            $data['pages'][]=array('num'=>$i,'url'=>($i>1?'/'.$i:''),'current'=>($i==$page?1:0));
            $needDots=true;
        } else if($needDots) {
            $data['pages'][]=array('num'=>'...','url'=>'','current'=>1);
            $needDots=false;
        }
        
    }
    // var_dump($data['pages']);
    $data['roorURI']=$app->request->getRootUri().'/';
    $app->db->freeze = true;
    $app->view()->appendData( $data );
    $app->render('last.tpl');
})->conditions(array('page' => '\d+'));
// Run app
$app->run();
