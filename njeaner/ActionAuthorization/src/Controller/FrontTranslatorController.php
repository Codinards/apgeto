<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Jean fils de Ntouoka2 <nguimjeaner@gmail.com>
 *
 * @Route("njeaner/dynamic/translation")
 */
class FrontTranslatorController extends AbstractController
{

    /**
     * @Route("/translate/from-{from}-to-{to}",
     *  name="njeaner_dynamic_translate_form_to",
     *  methods={"GET", "POST"},
     *  requirements={"from":"[a-z]{2,3}", "to":"[a-z]{2,3}"}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function translateFromOneToOne(Request $request, string $from, string $to): Response
    {
        return new Response();
    }
}
