<?php 
$data=[];
$dataMax=1000000;
$methods=['GET','POST','DELETE','PUT'];
for($i=0;$i<$dataMax;$i++) {
    $data[]=[
        'route' => '/' . substr(md5(rand()), 0, 20),
        'method' => $methods[rand(0, count($methods)-1)]
    ];
}
$times=[];
function st($wayName, $findName)
{
    if (!isset($GLOBALS['times'][$wayName]) || !is_array($GLOBALS['times'][$wayName])) {
        $GLOBALS['times'][$wayName]=[];
    }
    $GLOBALS['times'][$wayName][$findName]=[
            'st' => microtime(true),
            'en' => -1,
    ];
}
function en($wayName, $findName)
{
    $GLOBALS['times'][$wayName][$findName]['en']=microtime(true);
}


$ways=[
    'array_filter method'=> function ($find,$data) {
        
        $searchedValue=$find;
        $neededObject = array_filter(
            $data,
            function ($e) use (&$searchedValue) {
                return $e['route'] == $searchedValue;
            }
        );
        $neededObject=end($neededObject);
        return $neededObject;
    },

    'good old for loop in for loop' =>  function ($find,$data) {
        foreach ($data as $d) {
            if ($d['route']==$find) {
                return $d;
            }
        }
        return null;
    },

    'array_search method' => function ($find,$data) {
        $key=array_search($find, array_column($data, 'route'));
        if ($key!==false) {
            return $data[$key];
        }
        return null;
    }
];

function confirm($find,$result)
{
    if (is_array($result) && isset($result['route'])) {
        return $result['route']==$find;
    }
    return false;
}

$toFind=[
    'from start' => $data[0]['route'],
    'from mid' => $data[floor(count($data)/2)]['route'],
    'form end' => $data[floor(count($data)-2)]['route'],
];




foreach ($ways as $wayName => $way) {
    foreach ($toFind as $findName => $find) {
        st($wayName, $findName);
        $result=$way($find, $data);
        if (confirm($find, $result)) {
            en($wayName, $findName);
        }
    }
}

foreach ($times as $way=>$tests) {
    echo $way . PHP_EOL;
    $avarage=0;
    foreach ($tests as $n => $v) {
        echo ' - ' ;
    
        if ($v['en']==-1) {
            echo $n . ' not worked' . PHP_EOL;
            continue;
        }
        $c=$v['en']-$v['st'];
        $avarage+=$c;
        echo $n . ' : ' . number_format($c, 4) . PHP_EOL;
    }
    echo ' - Avarage: '. number_format(floor($avarage/count($tests)*100000)/100000, 4) . PHP_EOL;
    echo PHP_EOL . '-------------------------' . PHP_EOL . PHP_EOL;
}
