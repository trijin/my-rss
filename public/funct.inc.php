<?
function check_groups($db) {
	if($db->groups->where('active=1')->count()>0){
		foreach ($db->groups->where('active=1') as $group) {
			$query=array();
			// $subQuery[]=
			if(strlen($group['include'])>0) {
			    $inc=explode(', ',$group['include']);
			    foreach ($inc as $value) {
			        $value=trim($value);
			        if(strlen($value)>0) {
			            $query[]='title REGEXP "'.addcslashes($value, "\n\r\"").'"';
			        }

			    }
			}
			if(strlen($group['exclude'])>0) {
			    $inc=explode(', ',$group['exclude']);
			    foreach ($inc as $value) {
			        $value=trim($value);
			        if(strlen($value)>0) {
			            $query[]='title NOT REGEXP "'.addcslashes($value, "\n\r\"").'"';
			        }
			    }
			}
			$rows=$db->rss_items->where('groups_id IN (0,-1) AND '.implode(' AND ',$query).'');
			// echo (string)$rows.'<br/>';
			$rows->update(array('groups_id'=>$group['id']));
		}
	}
}
function ago_ru($timestamp) {
    $difference = time() - $timestamp;
    $periods = array(
        array('секунду', 'секунды', 'секунд'),
        array('минуту', 'минуты', 'минут'),
        array('час', 'часа', 'часов'),
        array('день', 'дня', 'дней'),
        array('неделю', 'недели', 'недель'),
        array('месяц', 'месяца', 'месяцев'),
        array('год', 'года', 'лет'),
        array('десятилетие', 'десятилетий', 'десятилетий'),
    );

    $lengths = array('60','60','24','7','4.35','12','10');

    for($j = 0; $difference >= $lengths[$j]; $j++)
        $difference /= $lengths[$j];

    $difference = round($difference);

    $cases = array (2, 0, 1, 1, 1, 2);
    $text = $periods[$j][ ($difference%100>4 && $difference%100<20)? 2: $cases[min($difference%10, 5)] ];
    return $difference.' '.$text . ' назад';
}

function ago($timestamp) {
    $difference = time() - $timestamp;
    $periods = array(
        array('sec','sec'),
        array('min','min'),
        array('h','h'),
        array('day','days'),
        array('week', 'weeks'),
        array('month', 'month'),
        array('year', 'years'),
        array('century', 'centuries'),
    );

    $lengths = array('60','60','24','7','4.35','12','100');

    for($j = 0; $difference >= $lengths[$j]; $j++)
        $difference /= $lengths[$j];

    $difference = round($difference);

    $text = $periods[$j][ ($difference>1)?1:0];
    return $difference.' '.$text . ' ago';
}