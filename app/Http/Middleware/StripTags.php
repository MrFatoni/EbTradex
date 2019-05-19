<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class StripTags extends TransformsRequest
{

    /**
     * Transform the given value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        $stripTags = config('commonconfig.strip_tags');
        if (in_array($key, $stripTags['escape_text'], true)) {
            return strip_tags($value, $stripTags['allowed_tag_for_escape_text']);
        }

        if (in_array($key, $stripTags['escape_full_text'], true)) {
            return strip_tags($value, $stripTags['allowed_tag_for_escape_full_text']);
        }

        return strip_tags($value);
    }
}
