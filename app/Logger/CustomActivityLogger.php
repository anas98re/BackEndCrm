<?php

namespace App\Logger;

use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Models\Activity;

class CustomActivityLogger extends ActivityLogger
{
    protected function attributeValuesToBeLogged(string $processingEvent): array
    {
        $properties = parent::attributeValuesToBeLogged($processingEvent);

        if (isset($properties['attributes'])) {
            $properties['attributes'] = $this->convertArabicCharacters($properties['attributes']);
        }

        if (isset($properties['old'])) {
            $properties['old'] = $this->convertArabicCharacters($properties['old']);
        }

        return $properties;
    }

    protected function convertArabicCharacters(array $data): array
    {
        $encodedData = json_encode($data, JSON_UNESCAPED_UNICODE);

        return json_decode(
            preg_replace_callback(
                '/\\\\u([0-9a-f]{4})/i',
                function ($match) {
                    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16');
                },
                $encodedData
            ),
            true
        );
    }
}
