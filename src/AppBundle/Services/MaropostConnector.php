<?php

namespace AppBundle\Services;

use GeneralBackend\CoreBundle\Entity\GlobalUser;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MaropostConnector
 * @package AppBundle\Services
 */
class MaropostConnector
{
    protected $serviceContainer;

    public static $gender = array(
        0 => "gender_female",
        1 => "gender_male"
    );

    public static $age = array(
        1 => "age_under_21",
        2 => "age_21_30",
        3 => "age_31_40",
        4 => "age_41_50",
        5 => "age_51_60",
        6 => "age_above_60"
    );

    const URL_BASE = "http://api.maropost.com/accounts/";
    const URL_ADD_REMOVE_TAGS = "/add_remove_tags.json";

    const URL_CONTACT = "/contacts/email.json?contact[email]=";
    const URL_SUBSCRIBE= "/lists/%d/contacts/%d.json";
    const URL_WORKFLOW_STOP = "/workflows/%d/stop/%d.json";
    const URL_WORKFLOW_START = "/workflows/%d/start/%d.json";

    /**
     * MaropostConnector constructor.
     *
     * @param ContainerInterface $containerInterface
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->serviceContainer = $containerInterface;
    }

    private function getToken()
    {
        return $this->serviceContainer->getParameter("maropost_auth_token");
    }

    private function getAccountId()
    {
        return $this->serviceContainer->getParameter("maropost_account_id");
    }

    private function getUrl($nextPart)
    {
        if(strpos($nextPart,"?") === false) {
            return self::URL_BASE . $this->getAccountId() . $nextPart . "?auth_token=" . $this->getToken();
        }
        return self::URL_BASE . $this->getAccountId() . $nextPart . "&auth_token=" . $this->getToken();
    }

    public function addTags(GlobalUser $user, $tags)
    {
        $tagsArray = array("add_tags"=>$tags);

        return $this->sendTags($user, $tagsArray);
    }

    public function removeTags(GlobalUser $user, $tags)
    {
        $tagsArray = array("remove_tags"=>$tags);

        return $this->sendTags($user, $tagsArray);
    }

    private function sendTags(GlobalUser $user, $tagsData)
    {
        $data = array("tags"=>array("email"=>$user->getEmail()));

        $data["tags"] = array_merge($data["tags"], $tagsData);

        $jsonData = json_encode($data);

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        $curl = curl_init($this->getUrl(self::URL_ADD_REMOVE_TAGS));

        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($curl,CURLOPT_POSTFIELDS,$jsonData);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);

        $response = curl_exec($curl);

        return $response == "Tags are removed/added successfully!";
    }

    public function getUserInfo(GlobalUser $user)
    {

//        $curl = curl_init($this->getUrl(self::URL_CONTACT . $user->getEmail()));
        $curl = curl_init($this->getUrl("/contacts/email.json?contact[email]=kostelecky@webvalley.com"));

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response,true);

        return $jsonResponse;
    }

    // http://api.maropost.com/accounts/476/workflows/:workflow_id/stop/:contact_id
    public function stopWorkflow(GlobalUser $user, $workflowId)
    {
        $curl = curl_init($this->getUrl(sprintf(self::URL_WORKFLOW_STOP, $workflowId, $this->getMaropostUserId($user))));

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response,true);

        return $jsonResponse;
    }

    // http://api.maropost.com/accounts/476/workflows/:workflow_id/start/:contact_id
    public function startWorkflow(GlobalUser $user, $workflowId)
    {
        $curl = curl_init($this->getUrl(sprintf(self::URL_WORKFLOW_START, $workflowId, $this->getMaropostUserId($user))));

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response,true);

        return $jsonResponse;
    }

    public function getMaropostUserId(GlobalUser $user)
    {
        $userInfo = $this->getUserInfo($user);

        if(is_null($userInfo))
            return 0;

        return isset($userInfo["id"])? $userInfo["id"] : 0;
    }

    public function subscribeLists(GlobalUser $user, $list)
    {
        $maropostUserId = $this->getMaropostUserId($user);

        if($maropostUserId == 0)
            return false;

        return $this->listAPI($maropostUserId, $list, true);
    }

    public function unsubscribeLists(GlobalUser $user, $list)
    {
        $maropostUserId = $this->getMaropostUserId($user);

        if($maropostUserId == 0)
            return false;

        return $this->listAPI($maropostUserId,$list, false);
    }

    private function listAPI($maropostUserId, $list, $subscribe)
    {
        $url = $this->getUrl(sprintf(self::URL_SUBSCRIBE,$list , $maropostUserId));

        $curl = curl_init($url);

        $headers = array(
            "Content-Type: application/json",
            "Accept: application/json",
        );

        $data = array(
            "contact" => array(
                "subscribe" => $subscribe
            )
        );

        $jsonData = json_encode($data);

        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($curl,CURLOPT_POSTFIELDS,$jsonData);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);

        $response = curl_exec($curl);

        $jsonResponse = json_decode($response,true);

        return $jsonResponse;
    }

    private function testRange($int, $min, $max)
    {
        return ($min<$int && $int<$max);
    }

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
