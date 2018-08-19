<?php
/**
 * Copyright 2018 The WildPHP Team
 *
 * You should have received a copy of the MIT license with the project.
 * See the LICENSE file for more information.
 */

namespace WildPHP\Modules\RegexReplace;


use WildPHP\Core\Channels\ChannelCollection;
use WildPHP\Core\ComponentContainer;
use WildPHP\Core\Connection\IRCMessages\PRIVMSG;
use WildPHP\Core\Connection\Queue;
use WildPHP\Core\Connection\TextFormatter;
use WildPHP\Core\EventEmitter;
use WildPHP\Core\Modules\BaseModule;

class RegexReplace extends BaseModule
{
    public function __construct(ComponentContainer $container)
    {
        EventEmitter::fromContainer($container)
            ->on('irc.line.in.privmsg', [$this, 'routeMessage']);

        $this->setContainer($container);
    }

    /**
     * @param PRIVMSG $incomingIrcMessage
     * @param Queue $queue
     * @throws \Yoshi2889\Container\NotFoundException
     */
    public function routeMessage(PRIVMSG $incomingIrcMessage, Queue $queue)
    {
        $text = $incomingIrcMessage->getMessage();

        $parseResult = $this->parseSedStyleReplacements($text);
        $channel = ChannelCollection::fromContainer($this->container)->findByChannelName($incomingIrcMessage->getChannel());

        /** @var StoredMessage[] $lastChannelMessages */
        $lastChannelMessages = $channel->_regexReplaceLastMessages ?? [];

        // Sort by newest first.
        $messages = array_reverse($lastChannelMessages);

        if ($parseResult instanceof Replacement) {
            if (!empty($messages)) {
                foreach ($messages as $lastChannelMessage) {
                    $preresult = preg_replace('/' . $parseResult->find . '/' . $parseResult->flags, $parseResult->replace, $lastChannelMessage->text);
                    if ($preresult == $lastChannelMessage->text)
                        continue;

                    $result = $preresult;
                    break;
                }

                if (empty($result) || empty($lastChannelMessage))
                    $msg = 'No matches found.';
                else
                    $msg = $lastChannelMessage->nickname . ': Did you mean to say: ' . TextFormatter::bold($result);

                $queue->privmsg($incomingIrcMessage->getChannel(), $msg);
                return;
            } else {
                $msg = 'I have no previous messages stored for this channel and therefore I cannot do text replacements, sorry.';
                $queue->privmsg($incomingIrcMessage->getChannel(), $msg);
                return;
            }
        }

        if (empty($channel->_regexReplaceLastMessages))
            $channel->_regexReplaceLastMessages = [];

        $channel->_regexReplaceLastMessages[] = new StoredMessage($incomingIrcMessage->getMessage(), $incomingIrcMessage->getNickname());

        if (count($channel->_regexReplaceLastMessages) > 10) {

            $channel->_regexReplaceLastMessages = array_slice($channel->_regexReplaceLastMessages, -10);
        }
    }

    /**
     * @param string $message
     * @return null|Replacement
     */
    public function parseSedStyleReplacements(string $message): ?Replacement
    {
        $regex = '/(?:^|\s)s\/((?:\\\\\/|[^\/])+)\/((?:\\\\\/|[^\/])*)(?:\/([ig]*))?/m';

        if (preg_match($regex, $message, $matches) !== 1)
            return null;

        $replacement = new Replacement();
        $replacement->find = $matches[1];
        
        // unescape the things preg_quote would normally escape/quote.
        $replacement->replace = preg_replace('/\\\\(\\\\|\+|\*|\?|\[|\]|\^|\$|\(|\)|\{|\}|\=|\!|\<|\>|\||\:|\-|\.|\/)/', '$1', $matches[2]);
        $replacement->flags = $matches[3] ?? '';

        return $replacement;
    }

    public static function getSupportedVersionConstraint(): string
    {
        return '^3.0.0';
    }
}
