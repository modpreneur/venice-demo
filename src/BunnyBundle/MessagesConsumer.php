<?php

namespace BunnyBundle;

use Trinity\Bundle\BunnyBundle\Annotation\Consumer;
use Venice\BunnyBundle\MessagesConsumer as VeniceMessagesConsumer;

/**
 * Class MessagesConsumer
 *
 * @Consumer(
 *     queue="client_1",
 *     maxMessages=100,
 *     maxSeconds=600.0,
 *     prefetchCount=1,
 *     method = "readMessage"
 * )
 */
class MessagesConsumer extends VeniceMessagesConsumer
{

}