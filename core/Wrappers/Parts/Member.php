<?php

namespace Core\Wrappers\Parts;

class Member extends Part
{
    private $_member = ['user', 'id', 'username', 'avatar', 'discriminator'];

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
