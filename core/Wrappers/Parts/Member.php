<?php

namespace Core\Wrappers\Parts;

/**
 * Issues with this part, leaving alone for now.
 */
class Member extends Part
{
    private $_member = ['id', 'username', 'discriminator'];

    /**
     * {@inheritdoc}
     */
    public function __construct($guild, $member)
    {
        if (substr($member, 0, 2) == '<@') {
            $member = $guild->members->get('id', $this->parseId($member));
        }

        parent::__construct($member);
    }

    /**
     * Returns the parent Member part.
     *
     * @return \Discord\Parts\User\Member
     */
    public function get()
    {
        return $this->part;
    }

    /**
     * Returns the user's avatar with a custom size.
     *
     * @param int $size
     *
     * @return string
     */
    public function avatar($size = 256)
    {
        $avatar = $this->user->avatar;
        $avatar = str_replace('=1024', '='.$size, $avatar);

        return $avatar;
    }

    /**
     * Parses a Discord Member ID into the ID number.
     *
     * @param string $idstr
     *
     * @return string
     */
    protected function parseId($idstr)
    {
        return rtrim(str_replace('<@', '', $idstr), '>');
    }

    /**
     * {@inheritdoc}
     */
    public function __get($property)
    {
        try {
            if (in_array($property, $this->_member)) {
                if ($property == 'user') {
                    return $this->part->user;
                }

                return $this->part->user->{$property};
            }

            return $this->part->{$property};
        } catch (\Exception $ex) {
            return;
        }
    }

    public function __toString()
    {
        return '<@'.$this->part->user->id.'>';
    }
}
