<?php

namespace App\Tools\Tontines;

use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\TontineurData;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

class TontineTool
{
    public function resolveCreateNewTontineMember(Request &$request, Tontine $tontineBase, User $admin, ?Tontine $oldTontine = null): Tontine
    {
        $tontine = $request->request->get('tontine');

        if ($tontine) {
            $tontineurs = $tontine['tontineurData'];
            foreach ($tontineurs as $key => $tontineur) {
                /** @var TontineurData $item */
                foreach ($tontineBase->getTontineurData() as $item) {
                    if ($oldTontine) $item->setTontine($oldTontine);
                    if ($item->getTontineur()->getName() == $tontineur['name']) {
                        if (isset($tontineur['isSelected'])) {
                            $demiNom = isset($tontineur['demiNom']);
                            $count = (int) $tontineur['count'] ?? null;
                            if ($demiNom) {
                                $item->setDemiNom(true);
                            }
                            if (!is_null($count)) {
                                $item->hydrateUnities(
                                    $tontineBase->getType()->getCotisation(),
                                    $admin,
                                    ($count > 0) ? $count : ($demiNom ? 0 : 1)
                                );
                            }
                        } else {
                            $tontineBase->removeTontineurData($item);
                            $tontineBase->removeTontineur($item->getTontineur());
                            unset($tontineurs[$key]);
                        }
                    }
                }
            }

            $tontineBase->setTontineurs(new ArrayCollection([...$tontineBase->getTontineurs()->toArray()]));
            $tontineBase->setTontineurData(new ArrayCollection([...$tontineBase->getTontineurData()->toArray()]));
            //dd($tontineurs);
            $tontine['tontineurData'] = [...$tontineurs];

            $request->request->set('tontine', $tontine);
        }

        //dd($tontineBase);
        return $tontineBase;
        //return $tontineBase->setTontineurs(new ArrayCollection(array_merge([], $tontineBase->getTontineurs()->toArray())));
    }

    public function resolveAddTontineMember(Request &$request, Tontine $tontineBase, Tontine $fakeTontine, User $admin): Tontine
    {
        $tontine = $request->request->get('tontine');
        $tontineData = new ArrayCollection($tontineBase->getTontineurData()->toArray());
        if ($tontine) {
            $tontineurs = $tontine['tontineurData'];
            //dd($tontineurs);
            $UsersIds = [];
            foreach ($tontineurs as $key => $tontineur) {
                if (isset($tontineur['isSelected'])) {
                    $data = (new TontineurData())
                        ->setTontine($tontineBase)
                        ->setTontineur($fakeTontine->getTontineurs()[$key])
                        //->setCount((int)$tontineur['count'])
                    ;
                    $demiNom = false;
                    if ($tontineur['demi_nom'] ?? null) {
                        $demiNom = true;
                    }

                    if ($oldData = $tontineBase->hasTontineurData($data)) {
                        //dump([$oldData->getTontineur()->getId(), $demiNom, $oldData->getCountDemiNom()]);
                        $oldData->mergeUnities(
                            $tontineBase->getType()->getCotisation(),
                            $admin,
                            $tontineur['count'],
                            true,
                            $demiNom
                        );
                    } else {
                        $data->setDemiNom($demiNom);
                        $data->hydrateUnities(
                            $tontineBase->getType()->getCotisation(),
                            $admin,
                            $tontineur['count']
                        );
                        $tontineBase->addTontineurData($data);
                        $newTontineur = $fakeTontine->getTontineurs()[$key]->addTontineurData($data);
                        $tontineBase->addTontineur($newTontineur);
                    }
                    //$tontineBase->getTontineurs()[$key];
                } else {
                    //unset($tontineurs[$key]);
                } /*else {
                    if (!$tontineBase->dataHasTontineur($key)) {
                        $tontineBase->deleteTontineurByIndex($key);
                    }
                }*/
            }
            $tontine['tontineurdata'] = $tontineurs;
            $request->request->set('tontine', $tontine);
        }
        return $tontineBase->setTontineurs(new ArrayCollection(array_merge([], $tontineBase->getTontineurs()->toArray())));

        /*if ($tontine) {
            $tontineurs = $tontine['tontineurs'];
            $UsersIds = [];
            foreach ($tontineurs as $key => $tontineur) {
                if (in_array($tontineur['user'], $UsersIds)) {
                    unset($tontineurs[$key]);
                    //$tontineBase->deleteTontineurByIndex($key);
                }
                if (!isset($tontineur['select'])) {
                    unset($tontineurs[$key]);
                    //$tontineBase->deleteTontineurByIndex($key);
                } else {
                    if ((int) $tontineur['count'] == 0) { //Si on a pa selectionner le nombre de nom ou pas de demin nom
                        // Si on a selectionner le demi nom
                        $tontineurs[$key]['count'] = 0;
                    }
                    $UsersIds[] = $tontineur['user'];
                }
            }
            $tontine['tontineurs'] = $tontineurs;
            $request->request->set('tontine', $tontine);
        }*/
        return $tontineBase;
    }
}
