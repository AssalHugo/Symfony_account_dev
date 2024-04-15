<?php

namespace App\Service;

class FlashBag
{

    /**
     * @param $message
     * @param $request
     * @return void
     */
    public function flashBagDanger($message, $request) {
        $session = $request->getSession();
        $session->getFlashBag()->add('message', $message);
        $session->set('statut', 'danger');
    }

    /**
     * @param $message
     * @param $request
     * @return void
     */
    public function flashBagSuccess($message, $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('message', $message);
        $session->set('statut', 'success');
    }
}