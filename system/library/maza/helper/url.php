<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
*/
namespace maza;

function getImageURL($image): string {
    if(is_file(DIR_IMAGE . $image)){
        return Registry::config('mz_store_url') . 'image/' . $image;
    }

    return '';
}

function parseVideoURL(string $url): array {
    // Youtube
    $youtube_id = '';

    if (preg_match('/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([\w\-]+)/i', $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match('/youtu.be\/([a-zA-Z0-9_]+)\??/i', $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if ($youtube_id) {
        return array(
            'url' => 'https://www.youtube.com/embed/' . $youtube_id,
            'thumb' => "https://img.youtube.com/vi/$youtube_id/maxresdefault.jpg",
        );
    }

    // Vimeo
    if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $url, $regs)){
        return array(
            'url' => 'https://player.vimeo.com/video/' . $regs[1],// . '?title=0&byline=0&portrait=0&badge=0&color=ffffff';
            'thumb' => "https://vumbnail.com/$regs[1].jpg",
        );
    }

    return array();
}