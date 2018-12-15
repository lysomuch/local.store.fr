<?php
namespace Silk\FacebookWallPost\Model\Source;

class WallPosts
{
    /**
     * Get the facebook settings
     *
     * @var \Silk\FacebookWallPost\Model\Source\FacebookSettings
     */
    protected $facebookSettings;

    /**
     *
     * @var \Silk\FacebookWallPost\Model\Source\RemoteUrl
     */
    protected $remoteUrl;

    /** @var \Magento\Framework\Stdlib\DateTime\Timezone */
    protected $timezone;

    /* 永久的facebook页面访问token */
    protected $page_access_token;

    public function __construct(
        \Silk\FacebookWallPost\Model\Source\FacebookSettings $facebookSettings,
        \Silk\FacebookWallPost\Model\Source\RemoteUrl $remoteUrl,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone
    ) {
        $this->facebookSettings = $facebookSettings;
        $this->remoteUrl = $remoteUrl;
        $this->timezone = $timezone;

        $this->page_access_token = $this->facebookSettings->getFacebookSetting('cms/facebook_wall_post/page_access_token');
    }

    /**
     * 获取帖子列表
     * @return array
     */
    public function getWallPosts()
    {
        $limit_number = $this->facebookSettings->getFacebookSetting('cms/facebook_wall_post/limit_number');
        $rs = json_decode($this->remoteUrl->getFacebookWallPost($this->page_access_token), true);

        if (empty($rs['feed']['data'])) return [];

        $list = [];
        $count = 0;
        foreach($rs['feed']['data'] as $item) {
            if( ! isset($item['message']) ) continue;

            $count++;
            if($count > $limit_number) break;

            //格式化创建时间
            $created_time = $this->timezone->date(new \DateTime($item['created_time']))->format('Y-m-d H:i:s');
            $list[] = [
                'message' => $item['message'],
                'created_time' => $created_time
            ];
        }

        return $list;
    }

    /**
     * 获取facebook主页信息（包括名字，头像，粉丝数量，主页URL地址）
     * @return array
     */
    public function getPageInfo()
    {
        $rs = json_decode($this->remoteUrl->getFacebookPageInfo($this->page_access_token), true);
        if( ! $rs) return [];

        $list = [
            'name' => (isset($rs['name']) ? $rs['name'] : ''),
            'picture' => (isset($rs['picture']['data']['url']) ? $rs['picture']['data']['url'] : ''),
            'fan_count' => (isset($rs['fan_count']) ? $rs['fan_count'] : 0),
            'page_url' => $this->facebookSettings->getFacebookSetting('cms/facebook_wall_post/page_url')
        ];

        return $list;
    }
}
