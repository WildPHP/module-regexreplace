<?php
/**
 * Copyright 2018 The WildPHP Team
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace WildPHP\Modules\RegexReplace;

class StoredMessage
{
    /**
     * @var string
     */
    public $text = '';

    /**
     * @var string
     */
    public $nickname = '';

    /**
     * StoredMessage constructor.
     * @param string $text
     * @param string $nickname
     */
    public function __construct(string $text, string $nickname)
    {
        $this->text = $text;
        $this->nickname = $nickname;
    }


}