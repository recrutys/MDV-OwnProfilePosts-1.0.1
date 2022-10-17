<?php

namespace MDV\OwnProfilePosts\XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\Reroute;

class Member extends XFCP_Member
{
    public function actionSelfProfilePosts(ParameterBag $params)
    {
        $selfPage = $this->filterPage($params->page);
        $selfPerPage = \XF::options()->messagesPerPage;

        $user = $this->assertViewableUser($params->user_id);

        $posts = $this->finder('XF:ProfilePost')
            ->where([
                ['message_state', '=', 'visible'],
                ['user_id', '=', $params->user_id],
                ['profile_user_id', '=', $params->user_id]
            ])
            ->order('post_date', 'DESC');

        $this->assertValidPage($selfPage, $selfPerPage, $posts->total(), 'members/self-profile-posts', $user);

        $viewParams = [
            'posts' => $posts->limitByPage($selfPage, $selfPerPage)->fetch(),
            'user' => $user,
            'selfPage' => $selfPage,
            'selfPerPage' => $selfPerPage,
            'selfTotal' => $posts->total()
        ];

        return $this->view('MDV\ViewPosts', 'mdv_opp_index', $viewParams);
    }
}