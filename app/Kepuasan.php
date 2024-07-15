<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kepuasan extends Model
{
    protected $fillable = [
        'umur', 'no_hp', 'jk', 'pendidikan', 'pekerjaan', 'penjamin', 'unit', 'pertanyaan1', 'pertanyaan2', 'pertanyaan3',
        'pertanyaan4', 'pertanyaan5', 'pertanyaan6', 'pertanyaan7', 'pertanyaan8', 'pertanyaan9', 'pertanyaan10', 'saran'
    ];

    public static function createPreview($text, $limit)
    {
        $text = preg_replace('/\[\/?(?:b|i|u|s|center|quote|url|ul|ol|list|li|\*|code|table|tr|th|td|youtube|gvideo|(?:(?:size|color|quote|name|url|img)[^\]]*))\]/', '', $text);

        if (strlen($text) > $limit) return substr($text, 0, $limit) . "...";
        return $text;
    }
}
