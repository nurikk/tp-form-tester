<?php
$DATA_SOURCE = $_POST;
if ($DATA_SOURCE) {
    $url = $DATA_SOURCE['url'];

    $replacement_type = $DATA_SOURCE['replacement_type'];

    $html = get_url($url);
    $html = prepare_base($html, $url);

    $insert_code = $DATA_SOURCE['code'];

    switch ($replacement_type) {
        case 'replace':
            $html = find_and_replace_old_form($insert_code, $html);
            break;
        case 'body_begin':
            $html = find_replace_tag($insert_code, $html, 'body');
            break;
        case 'body_end':
            $html = find_replace_tag($insert_code, $html, '\/body', true);
            break;
        default:
            break;
    }
    echo $html;

}

function get_url($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $out = curl_exec($curl);
    curl_close($curl);
    return $out;
}

function find_and_replace_old_form($insert, $to)
{
    $find_pattern = '/<script\b[^>]*>\s*SETTINGS_HOST(.*?)<\/script>/is';
    return preg_replace($find_pattern, $insert, $to);
}

function find_replace_tag($insert, $to, $tag, $before = false)
{
    $find_pattern = '/<' . $tag . '[^>]*?>/';
    if ($before) {
        $insert = $insert . '$0';
    } else {
        $insert = '$0' . $insert;
    }
    return preg_replace($find_pattern, $insert, $to);
}

function prepare_base($html, $url)
{
    $base_tag = '<base href="http://' . parse_url($url, PHP_URL_HOST) . '/">';
    $find_pattern = '/<head[^>]*?>/';
    return preg_replace($find_pattern, $base_tag, $html);
}