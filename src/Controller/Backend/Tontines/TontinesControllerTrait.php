<?php

namespace App\Controller\Backend\Tontines;

use App\Controller\Exceptions\ControllerException;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Unity;

trait TontinesControllerTrait
{
    /**
     * Undocumented function
     *
     * @param Tontine|Unity $tontine
     * @return boolean
     */
    private function tontineIsCurrent($tontine): bool
    {
        if ((!$tontine instanceof Tontine) and (!$tontine instanceof Unity)) {
            throw new ControllerException(
                __METHOD__ . 'argument "$tontine" must be an instance of "' . Tontine::class . '" or of "'
                    . Unity::class . '", instance of "' . get_class($tontine) . '" is given'
            );
        }
        return (bool) $tontine->getIsCurrent();
    }


    private function tontineIsValid($tontine, $request)
    {
        $tontine = $tontine instanceof Unity ? $tontine->getTontine() : $tontine;
        $this->throwRedirectRequest(
            $this->tontineIsCurrent($tontine) === false,
            $this->generateUrl('app_backend_tontine_show', ['lang' => $request->getLocale(), 'id' => $tontine->getId()]),
            $this->trans('tontine.not.current'),
            true
        );
    }
}
