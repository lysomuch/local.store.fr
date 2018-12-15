<?php
namespace Silk\FacebookWallPost\Model\Source;

use Zend\Http\Client;
use Zend\Http\Request;

class RemoteUrl
{
    /**
     * 获取facebook主页最近的帖子列表（只能获取到文本内容，图片视频等获取不到）
     * @param string $accessToken 页面永久性访问token
     * @return string
     */
    public function getFacebookWallPost($accessToken='')
    {
        $request = new Request();
        $request->setUri('https://graph.facebook.com/v3.0/me?fields=feed.limit(10)&access_token=' . $accessToken);
        $client = new Client();
        $client->setMethod('GET');
        $response = $client->send($request);

        if ($response->isSuccess()) {
            return $response->getBody();
        }

        return '';
    }

    /**
     * 获取facebook主页信息（包括名字，头像，粉丝数量）
     * @param string $accessToken 页面永久性访问token
     * @return string
     */
    public function getFacebookPageInfo($accessToken='')
    {
        $request = new Request();
        $request->setUri('https://graph.facebook.com/v3.0/me?fields=name,picture,fan_count&access_token=' . $accessToken);
        $client = new Client();
        $client->setMethod('GET');
        $response = $client->send($request);

        if ($response->isSuccess()) {
            return $response->getBody();
        }

        return '';
    }
}
