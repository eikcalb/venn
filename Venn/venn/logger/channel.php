<?php

namespace Venn\logger;

/**
 * This provides a sink to handle @see Logger events.
 * This can be used to save logs into files, databases or as variables within the application.
 * 
 *
 * @author LORD AGWA
 */
interface Channel {
    
    public function getName();
    
    public function onNewLog($log);
    
    
}
