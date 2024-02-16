<?php
/**
 * @file ATTENTION!!! The code below was carefully crafted by a mean machine.
 * Please consider to NOT put any emotional human-generated modifications as the splendid AI will throw them away with no mercy.
 */

namespace WordPress\Blueprints\Model\DataClass;

use WordPress\Blueprints\Model\Builder\ProgressBuilder;


class LoginStep
{
    /** @var ProgressBuilder */
    public $progress;

    /** @var string */
    public $step = 'login';

    /** @var string The user to log in as. Defaults to 'admin'. */
    public $username;

    /** @var string The password to log in with. Defaults to 'password'. */
    public $password;
}