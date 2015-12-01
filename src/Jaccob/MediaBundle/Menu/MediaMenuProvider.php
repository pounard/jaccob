<?php

namespace Jaccob\MediaBundle\Menu;

use Jaccob\AccountBundle\Menu\AbstractSecurityAwareMenuProvider;

class MediaMenuProvider extends AbstractSecurityAwareMenuProvider
{
    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        switch ($name) {

            case 'main.media':
                $account = $this->getCurrentUserAccount();

                if ($account) {
                    $menu = $this->factory->createItem('Tasks', ['route' => 'jaccob_media.home']);

                    return $menu;
                }
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        switch ($name) {

            case 'main.media':
                return true;

            default:
                return false;
        }
    }
}
