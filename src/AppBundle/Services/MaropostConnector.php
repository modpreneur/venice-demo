<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MaropostConnector
 * @package AppBundle\Services
 */
class MaropostConnector
{
    protected $serviceContainer;

    public static $gender = [
        0 => 'gender_female',
        1 => 'gender_male'
    ];

    public static $age = [
        1 => 'age_under_21',
        2 => 'age_21_30',
        3 => 'age_31_40',
        4 => 'age_41_50',
        5 => 'age_51_60',
        6 => 'age_above_60'
    ];

    const URL_BASE = 'http://api.maropost.com/accounts/';
    const URL_ADD_REMOVE_TAGS = '/add_remove_tags.json';

    const URL_CONTACT = '/contacts/email.json?contact[email]=';
    const URL_SUBSCRIBE= '/lists/%d/contacts/%d.json';
    const URL_WORKFLOW_STOP = '/workflows/%d/stop/%d.json';
    const URL_WORKFLOW_START = '/workflows/%d/start/%d.json';

    /**
     * MaropostConnector constructor.
     *
     * @param ContainerInterface $containerInterface
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->serviceContainer = $containerInterface;
    }


    /**
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getToken()
    {
        return $this->serviceContainer->getParameter('maropost_auth_token');
    }


    /**
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getAccountId()
    {
        return $this->serviceContainer->getParameter('maropost_account_id');
    }


    /**
     * @param $nextPart
     *
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getUrl($nextPart)
    {
        if (strpos($nextPart, '?') === false) {
            return self::URL_BASE . $this->getAccountId() . $nextPart . '?auth_token=' . $this->getToken();
        }
        return self::URL_BASE . $this->getAccountId() . $nextPart . '&auth_token=' . $this->getToken();
    }


    /**
     * @param User $user
     * @param $tags
     *
     * @return bool
     */
    public function addTags(User $user, $tags)
    {
        $tagsArray = ['add_tags' => $tags];

        return $this->sendTags($user, $tagsArray);
    }


    /**
     * @param User $user
     * @param $tags
     *
     * @return bool
     */
    public function removeTags(User $user, $tags)
    {
        $tagsArray = ['remove_tags' => $tags];

        return $this->sendTags($user, $tagsArray);
    }


    /**
     * @param User $user
     * @param $tagsData
     *
     * @return bool
     */
    private function sendTags(User $user, $tagsData)
    {
        $data = ['tags' => ['email' =>$user->getEmail()]];

        $data['tags'] = array_merge($data['tags'], $tagsData);

        $jsonData = json_encode($data);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $curl = curl_init($this->getUrl(self::URL_ADD_REMOVE_TAGS));

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        return $response == 'Tags are removed/added successfully!';
    }

    public function getUserInfo(User $user)
    {

//        $curl = curl_init($this->getUrl(self::URL_CONTACT . $user->getEmail()));
        $curl = curl_init($this->getUrl('/contacts/email.json?contact[email]=kostelecky@webvalley.com'));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

    // http://api.maropost.com/accounts/476/workflows/:workflow_id/stop/:contact_id
    public function stopWorkflow(User $user, $workflowId)
    {
        $curl = curl_init($this->getUrl(sprintf(self::URL_WORKFLOW_STOP, $workflowId, $this->getMaropostUserId($user))));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }


    // http://api.maropost.com/accounts/476/workflows/:workflow_id/start/:contact_id
    public function startWorkflow(User $user, $workflowId)
    {
        $curl = curl_init($this->getUrl(sprintf(self::URL_WORKFLOW_START, $workflowId, $this->getMaropostUserId($user))));

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
        );

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }


    /**
     * @param User $user
     *
     * @return int
     */
    public function getMaropostUserId(User $user)
    {
        $userInfo = $this->getUserInfo($user);

        if (is_null($userInfo)) {
            return 0;
        }

        return isset($userInfo['id'])? $userInfo['id'] : 0;
    }


    /**
     * @param User $user
     * @param $list
     *
     * @return bool|mixed
     */
    public function subscribeLists(User $user, $list)
    {
        $maropostUserId = $this->getMaropostUserId($user);

        if ($maropostUserId === 0) {
            return false;
        }

        return $this->listAPI($maropostUserId, $list, true);
    }


    /**
     * @param User $user
     * @param $list
     *
     * @return bool|mixed
     */
    public function unsubscribeLists(User $user, $list)
    {
        $maropostUserId = $this->getMaropostUserId($user);

        if ($maropostUserId === 0) {
            return false;
        }

        return $this->listAPI($maropostUserId, $list, false);
    }


    /**
     * @param $maropostUserId
     * @param $list
     * @param $subscribe
     *
     * @return mixed
     */
    private function listAPI($maropostUserId, $list, $subscribe)
    {
        $url = $this->getUrl(sprintf(self::URL_SUBSCRIBE, $list, $maropostUserId));

        $curl = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $data = [
            'contact' => [
                'subscribe' => $subscribe
            ]
        ];

        $jsonData = json_encode($data);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }


    /**
     * @param $int
     * @param $min
     * @param $max
     *
     * @return bool
     */
    private function testRange($int, $min, $max)
    {
        return ($min<$int && $int<$max);
    }


    /**
     * @param $age int??
     *
     * @return string
     */
    public function getAgeTag($age)
    {
        $tag = '';
        if ($age < 21) {
            $tag = 'age_under_21';
        } elseif ($this->testRange($age, 20, 31)) {
            $tag = 'age_21_30';
        } elseif ($this->testRange($age, 30, 41)) {
            $tag = 'age_31_40';
        } elseif ($this->testRange($age, 40, 51)) {
            $tag = 'age_41_50';
        } elseif ($this->testRange($age, 50, 61)) {
            $tag = 'age_51_60';
        } elseif ($age > 60) {
            $tag = 'age_above_60';
        }
        return $tag;
    }
}
