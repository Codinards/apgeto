<?php

namespace App\Controller;

use App\Entity\JsonEntity\FunctionalitiesJsonEntity;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Tools\AppConstants;
use App\Tools\Cache\CacheRetriever;
use DateTime;
use Njeaner\UserRoleBundle\Annotations\Module;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Njeaner\UserRoleBundle\Annotations\RouteAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Module(name="base", is_activated=true)
 */
class HomeController extends BaseController
{

    static $devOriginalKeys = [
        'trans' => [],
        'module' => [],
        'submod' => []
    ];

    private function unsetEmpties(array $devLevel)
    {
        foreach ($devLevel as $key => $elt) {
            foreach ($elt as $k => $v) {
                if (is_array($v)) {
                    if (empty($v)) unset($devLevel[$key][$k]);
                    else {
                        foreach ($v as $ke => $val) {
                            if (is_array($val)) {
                                if (empty($val)) {
                                    unset($devLevel[$key][$k][$ke]);
                                } else {
                                    if (is_array($val)) {
                                        foreach ($val as $lastK => $lastV) {
                                            if (is_array($lastV)) {
                                                if (empty($lastV)) {
                                                    unset($devLevel[$key][$k][$ke][$lastK]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $devLevel;
    }

    public function recursiveTrans(array $data, int $i = 0): array
    {
        $lastI = $i;
        foreach ($data as $key => $value) {
            if (!is_int($key)) {
                $transKey = $this->trans($key);
                if (is_array($value)) {

                    //if ($i == 0) self::$devOriginalKeys['state'][] = $transKey;
                    if ($i == 1) self::$devOriginalKeys['module'] = array_unique(array_merge(self::$devOriginalKeys['module'], [$transKey]));
                    elseif ($i == 2) self::$devOriginalKeys['submod'] = array_unique(array_merge(self::$devOriginalKeys['submod'], [$transKey]));

                    $data[$transKey] = $this->recursiveTrans($value, $lastI + 1);
                    self::$devOriginalKeys['trans'][$transKey] = $key;
                    if ($transKey !== $key) unset($data[$key]);
                } else {
                    $data[$transKey] = $this->trans($value);
                    if ($transKey !== $key) {
                        unset($data[$key]);
                    }
                }
            } else {
                $data[$key] = $this->trans($value);
            }
        }
        return $data;
    }

    /**
     * @Route("/{_locale}/home", name="home", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     * @RouteAction(name="home", title="see.home.action", targets={"all"}, is_updatable=false, has_auth=false)
     */
    public function home(Request $request): Response
    {
        $devLevel = [];
        if ($request->server->get("APP_ENV")) {
            $devLevel = (new FunctionalitiesJsonEntity)->getData();
            for ($i = 0; $i < 3; $i++) {
                $devLevel = $this->unsetEmpties($devLevel);
            }
        }


        $devLevel = $this->recursiveTrans($devLevel);
        self::$devOriginalKeys['env'] = $request->server->get('APP_ENV');
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'functionalities' => $request->query->get('functionalities'),
            'devLevels' => $devLevel,
            'devKeys' => self::$devOriginalKeys,
            'encode' => json_encode(array_filter(
                $devLevel,
                function ($item, $key) {
                    return ($key !== "functionalities" and $key !== 'fonctionalités' and $key !== 'funcionalidades');
                },
                ARRAY_FILTER_USE_BOTH
            ))
        ]);
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->redirectToRoute('home', ['_locale' => AppConstants::$DEFAULT_LANGUAGE_KEY]);
    }

    /**
     * @Route("/todo", name="app_todo", methods={"GET"}, requirements={"_locale":"en|fr|es|it|pt"})
     */
    public function todo()
    {
        return $this->render('/todo/index.html.twig');
    }
}
