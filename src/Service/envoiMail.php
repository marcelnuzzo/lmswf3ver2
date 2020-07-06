<?php

namespace App\Service;

class envoiMail
{
    public function envoi($body)
    {
        $message = (new \Swift_Message('lmswf3'))
                ->setFrom('nuzzomarcel358@gmail.com')
                ->setTo('nuzzo.marcel@aliceadsl.fr')
                ->setBody($body,
                        'text/html'
            );
            
       return $message;

    }

}