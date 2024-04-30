<?php

namespace App\Logger;

use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\App;

class CustomActivityLogger extends ActivityLogger
{
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
        info('nnnnnn');
    }

    protected function attributesToBeLogged($subject): array
    {
        info('mmmmmmm');
        $model = $subject;

        $attributes = $model->getDirty();

        // Decode Unicode escape sequences for the 'groupName' attribute
        if (isset($attributes['groupName']) && $this->isAttributeArabic($attributes['groupName'])) {
            $attributes['groupName'] = $this->decodeUnicodeEscapes($attributes['groupName']);
        }

        return $this->cleanAttributes($attributes);
    }

    // Rest of the class implementation...

    protected function isAttributeArabic($attribute): bool
    {
        // Implement your logic to determine if the attribute is Arabic
        // For example, you can check if the attribute contains Arabic characters
        // and return true if it does, otherwise return false.
        // Modify this logic based on your requirements.

        return preg_match('/\p{Arabic}/u', $attribute) === 1;
    }

    protected function decodeUnicodeEscapes($string): string
    {
        return json_decode('"' . $string . '"');
    }
}
