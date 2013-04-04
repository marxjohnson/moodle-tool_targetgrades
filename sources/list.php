<?php 
$dir = scandir('../');
sort($dir);
$fileinfo = array();
$indexes[] = array();
$anchors[] = array();
foreach ($dir as $d) {
    $fileinfo[$d] = pathinfo($d);
    if (is_file('../'.$d)) {
        if ($d != 'index.html' && $fileinfo[$d]['extension'] == 'html') {
            $filename = $fileinfo[$d]['basename'];
            $contents = file_get_contents('../'.$d);
            preg_match('/^## ?(.*)/m', $contents, $matches);
            if (!empty($matches)) {
                $indexes['index.html'][$filename] = $matches[1];
                $indexes[$filename] = array();
                preg_match_all('/^### ?<a name="(.+?)">(.+?)<\/a>/m', $contents, $matches, PREG_SET_ORDER);
                if (!empty($matches[0])) {
                    foreach ($matches as $key => $match) {
                        $indexes[$filename][$match[1]] = $match[2];
                        $heading = strstr($contents, $match[0]);
                        if (isset($matches[$key+1])) {
                            $heading = strstr($heading, $matches[$key+1][0], true);
                        }
                        preg_match_all('/^#### ?<a name="(.+?)">(.+?)<\/a>/m', $heading, $subs, PREG_SET_ORDER);
                        foreach ($subs as $sub) {
                            $anchors[$filename][$match[1]][$sub[1]] = $sub[2];
                        }
                    }
                }
            }
        }
    }
}

file_put_contents('dexy--index-links.json', json_encode((object)$indexes));
file_put_contents('dexy--anchor-links.json', json_encode((object)$anchors));

?>