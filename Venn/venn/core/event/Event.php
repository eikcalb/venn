<?php

/*
 * (c) Agwa Israel Onome <eikcalb.agwa.io> 2018
 *  Part of Venn
 */

namespace Venn\core\event;

use Symfony\Component\EventDispatcher\Event as Event2;

/**
 * Description of Event
 *
 * @author Agwa Israel Onome <eikcalb.agwa.io>
 */
class Event extends Event2 {

    const ROUTE_MATCHED = "router.route_found";
    const KERNEL_BOOTSTRAP = "kernel.start_up";
    const KERNEL_CLEANUP = "kernel.clean_up";

    public $data = null;

}
