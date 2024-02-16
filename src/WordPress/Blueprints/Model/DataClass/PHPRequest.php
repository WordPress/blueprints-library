<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\PHPRequestFilesAdditionalPropertiesBuilder;


class PHPRequest
{
    /** @var string */
    public $method;

    /** @var string Request path or absolute URL. */
    public $url;

    /** @var string[] */
    public $headers;

    /** @var PHPRequestFilesAdditionalPropertiesBuilder[] Uploaded files */
    public $files;

    /** @var string Request body without the files. */
    public $body;

    /** @var array Form data. If set, the request body will be ignored and the content-type header will be set to `application/x-www-form-urlencoded`. */
    public $formData;
}