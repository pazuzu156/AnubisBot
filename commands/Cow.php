<?php

namespace Commands;

use Core\Command;
use Core\Parameters;

class Cow extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'cow';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Display a cow with your message';

    /**
     * Default length of bubble text.
     *
     * @var int
     */
    private $_defaultLength = 40;

    /**
     * Displays a cow with your message in a speech bubble.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function say(Parameters $p)
    {
        if ($p->count()) {
            $cow = $this->buildBubble($p->all())."\n".$this->buildCow();
            $this->channel->sendMessage("```$cow```");
        } else {
            $this->message->reply('You forgot to give me something to say!');
        }
    }

    /**
     * Displays a cow with your message in a thought bubble.
     *
     * @param \Core\Parameters $p
     *
     * @return void
     */
    public function think(Parameters $p)
    {
        if ($p->count()) {
            $cow = $this->buildBubble($p->all())."\n".$this->buildCow(true);
            $this->channel->sendMessage("```$cow```");
        } else {
            $this->message->reply('You forgot to give me something to say!');
        }
    }

    /**
     * Builds the cow ASCII.
     *
     * @param bool $thought
     *
     * @return string
     */
    private function buildCow($thought = false)
    {
        $bubbleTrail = ($thought) ? 'o' : '\\';

        $cow = <<<EOC
    $bubbleTrail  ^__^
     $bubbleTrail (oo)\_______
       (__)\       )\\/\\
           ||----w |
           ||     ||
EOC;

        return $cow;
    }

    /**
     * Builds the speech bubble.
     *
     * @param array $params
     *
     * @return string
     */
    private function buildBubble(array $params)
    {
        $string = implode(' ', $params);
        $this->normalize($string);

        $lines = explode("\n", $string);

        $borderSize = count(str_split($lines[0]));

        // add ____ at top of  bubble
        $bubble = '  ';
        $i = 0;
        while ($i < $borderSize) {
            $bubble .= '_';
            $i++;
        }

        $bubble .= "\n";

        // add orders onto lines for the bubble
        for ($i = 0; $i < count($lines); $i++) {
            $border = $this->getBorder($lines, $i);

            $bubble .= $border[0].' '.$lines[$i].' '.$border[1]."\n";
        }

        // add ---- at bottom of bubble
        $bubble .= '  ';
        $j = 0;
        while ($j < $borderSize) {
            $bubble .= '-';
            $j++;
        }

        return $bubble;
    }

    /**
     * Normalizes string for bubble.
     *
     * @param string &$string
     *
     * @return void
     */
    private function normalize(&$string)
    {
        $str = wordwrap($string, $this->_defaultLength);
        $exp = explode("\n", $str);

        $totalLength = count(str_split($exp[0]));

        for ($i = 0; $i < count($exp); $i++) {
            $count = count(str_split($exp[$i]));
            $spaceCount = $totalLength - $count;
            $append = '';
            for ($j = 0; $j < $spaceCount; $j++) {
                $append .= ' ';
            }
            $exp[$i] = $exp[$i].$append;
        }

        $string = wordwrap(implode("\n", $exp), $this->_defaultLength);
    }

    /**
     * Gets correct border for bubble.
     *
     * @param array $lines
     * @param int   $index
     *
     * @return array
     */
    private function getBorder($lines, $index)
    {
        if (count($lines) < 2) {
            return ['<', '>'];
        } elseif ($index == 0) {
            return ['/', '\\'];
        } elseif ($index == count($lines) - 1) {
            return ['\\', '/'];
        } else {
            return ['|', '|'];
        }
    }
}
