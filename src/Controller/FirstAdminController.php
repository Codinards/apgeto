<?php

namespace App\Controller;

use App\Entity\Main\Operations\Type;
use App\Entity\Main\Users\User;
use App\Entity\Main\Funds\Fund;
use App\Entity\Utils\FirstConfigEntity;
use App\Form\Users\FirstAdminType;
use App\Tools\AppConstants;
use App\Tools\DirectoryResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Tools\Entity\BaseRoleProperties;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class FirstAdminController extends BaseController
{
    /**
     * @Route("/{_locale}/first/admin/registration", name="first_admin_registration", requirements={"_locale":"en|fr|es|it|pt"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // $this->seedUsers($encoder);
        $this->throwRedirectRequest($request->getSession()->get('has_first_admin') ?? false, 'home');

        $firstConfig = new FirstConfigEntity();
        $form = $this->createForm(FirstAdminType::class, $firstConfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $manager = $this->getManager();
            /** @var UploadedFile $logo */
            $logo = $firstConfig->getLogo();
            $extension = $logo ? $logo->getClientOriginalExtension() : null;
            $filename =  (($logo and $extension) ? 'logo.' . $extension : 'default_logo.ico');

            $appConstant = AppConstants::getInstance();
            $appConstant::$SIGLE = $firstConfig->getAppName();
            $appConstant::$LOGO = '/uploads/' . $filename;
            $appConstant::$DEFAULT_LANGUAGE_KEY = $firstConfig->getDefaultLanguage();
            $appConstant::$MONEY_DEVISE = $firstConfig->getMoneyDevise();
            $appConstant::$FUND_CAN_BE_NEGATIVE = $firstConfig->getFundCanBeNegative() ?? false;
            $appConstant::$LOAN_BASE_FUND = $firstConfig->getBaseFundAmountToLoan() ?? 0;
            $appConstant::$ACCOUNT_BASE_FUND = $firstConfig->getAccountBaseAmount() ?? 0;
            $appConstant::$USER_MULTIPLE_LOAN = $firstConfig->getUserCanGetMultipleLoan() ?? false;
            $appConstant::$LOAN_BASE_FUND = $firstConfig->getBaseFundAmountToLoan() ?? 0;
            $appConstant::$TONTINEUR_MAX_COUNT = $firstConfig->getUnityMaxCount() ?? 10;
            if (
                stripos($appConstant::$LOGO, 'default_logo.ico') === false
                && file_exists($file = DirectoryResolver::getDirectory('public', false) . $appConstant::$LOGO)
            ) {
                unlink($file);
            }
            if ($logo) {
                $logo->move(DirectoryResolver::getDirectory('public/uploads', false), $filename);
            }

            $appConstant->save();
            $manager->persist(
                $user = (new User)
                    ->setPseudo($firstConfig->getPseudo())
                    ->setUsername($firstConfig->getUsername())
                    ->setPassword($firstConfig->getPassword())
                    ->setAddress($firstConfig->getAddress())
                    ->setTelephone($firstConfig->getTelephone())
                    ->setRole(
                        $this->getRoleRepository()->findOneBy(['name' => 'ROLE_SUPERADMIN'])
                    )
            );
            $user->setAdmin($user);


            /** Database commit */
            $manager->flush();
            return $this->redirectToRoute('home', ['_locale' => $appConstant::$DEFAULT_LANGUAGE_KEY ?? $request->getLocale()]);
        }

        return $this->render('first_admin/register.html.twig', [
            'title' => $this->trans('first admin registration'),
            'form' => $form->createView(),
        ]);
    }


    public function seedUsers(UserPasswordEncoderInterface $encoder){
        $data = [
            ["name" => "Djuikoo Théophile", "role" => BaseRoleProperties::ROLE_USER, "fond" => 3_120_140],
            ["name" => "Azebaze Fidel", "role" => BaseRoleProperties::ROLE_USER, "fond" => 57_689],
            ["name" => "Djuikoo Leonie", "role" => BaseRoleProperties::ROLE_USER, "fond" => 3_183_938],
            ["name" => "Zebaze Pierre Marie", "role" => BaseRoleProperties::ROLE_USER, "fond" => 618_772],
            ["name" => "Tsopbeng Bertin", "role" => BaseRoleProperties::ROLE_USER, "fond" => 371_873],
            ["name" => "Kamgou Fabien", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  4_446_042],
            ["name" => "Tsopgni Bruno", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  45_820],
            ["name" => "Nguefack Marc", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  60_239],
            ["name" => "Kitio Joseph", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  1403],
            ["name" => "Bogning Kenfack Victorien", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  404_397],
            ["name" => "Likemo Jacqueline", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  1_394_838],
            ["name" => "Donfack Hyppolite", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  915_737],
            ["name" => "Tsopgni Marie Claire", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  44_807],
            ["name" => "Donfack Marie Madeleine", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  70_994],
            ["name" => "Nguefack Carine", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  84_904],
            ["name" => "Tsobeng Mireine", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  73_062],
            ["name" => "Nguepi Rodrigue", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  42_320],
            ["name" => "Bogning Jean Pierre", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  65_260],
            ["name" => "Zefack Mireille", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  11_998],
            ["name" => "Tiago Fabien", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  -77_330],
            ["name" => "Zebaze Yolande", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  575_384],
            ["name" => "Kitio Jean Noêl", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  -57_729],
            ["name" => "Azebaze Rufine", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  91_167],
            ["name" => "Ngoufack Pascaline", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  462_505],
            ["name" => "Nguetsop Alvine", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  42_056],
            ["name" => "Feujio Viviane", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  122_993],
            ["name" => "Azambou Blaise Pascal", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  884_078],
            ["name" => "Jatsa Arlette", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  816_558],
            ["name" => "Jatsa Louis", "role" => BaseRoleProperties::ROLE_USER, "fond" => 84_805],            
            ["name" => "Djiofack Alain Thomas", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  2_705_874],
            ["name" => "Donkeng Marie Yvette", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  54_555],
            ["name" => "Kana Sokeng Francis", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  123_828],
            ["name" => "Voufo Louis Bertin", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  43_380],
            ["name" => "Nguezang Pierre Roger", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  49_664],
            ["name" => "Dongmo Elvis", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  487_607],
            ["name" => "Ngapgho Denise", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  1_069_357],
            ["name" => "Kenfack Chantale", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  57_101],
            ["name" => "Momo Leonie", "role" => BaseRoleProperties::ROLE_USER, "fond" =>  64_185],
            ["name" => "Tiomo Donnatchi Severin", "role" => BaseRoleProperties::ROLE_USER, "fond" => 1_030_646],
            ["name" => "Tiofack William Edouard", "role" => BaseRoleProperties::ROLE_USER, "fond" => 1_362_465],
            ["name" => "Dongmo Eveline", "role" => BaseRoleProperties::ROLE_USER, "fond" => 9_600],
            ["name" => "Kitio Alvine", "role" => BaseRoleProperties::ROLE_USER, "fond" => 60_992],
            ["name" => "Kenfack Celestine", "role" => BaseRoleProperties::ROLE_USER, "fond" => 45_800],
            ["name" => "Nguimenang Laure", "role" => BaseRoleProperties::ROLE_USER, "fond" => 73_202],
            ["name" => "Nguimfack Nguimzap Jean", "role" => BaseRoleProperties::ROLE_SUPERADMIN, "fond" => 996_974],
            ["name" => "Kemda Pierre", "role" => BaseRoleProperties::ROLE_USER, "fond" => 72_208],
            ["name" => "Kana Albert", "role" => BaseRoleProperties::ROLE_USER, "fond" => 63_959],
            ["name" => "Nguimezap Leonel Leonid", "role" => BaseRoleProperties::ROLE_USER, "fond" => 2_722],
            ["name" => "Donfack Gladice", "role" => BaseRoleProperties::ROLE_USER, "fond" => 50_241],
            ["name" => "Tsopjio Olivier", "role" => BaseRoleProperties::ROLE_USER, "fond" => 969_440],
            ["name" => "Azambou Rochelle", "role" => BaseRoleProperties::ROLE_USER, "fond" => 964_137],
            ["name" => "Nguena Alex", "role" => BaseRoleProperties::ROLE_USER, "fond" => 83_471],
            ["name" => "Nguimeya Vincent", "role" => BaseRoleProperties::ROLE_USER, "fond" => 1_416_020],
            ["name" => "Djiatsa Simplice", "role" => BaseRoleProperties::ROLE_USER, "fond" => 38_382],
            ["name" => "Bogning Edvige", "role" => BaseRoleProperties::ROLE_USER, "fond" => 380_922],
            ["name" => "Tsafack Gervais", "role" => BaseRoleProperties::ROLE_USER, "fond" => 309_571],
            ["name" => "Nguepi Blaise", "role" => BaseRoleProperties::ROLE_USER, "fond" => 73_800],
            ["name" => "Tsafack Iriane Vicky", "role" => BaseRoleProperties::ROLE_USER, "fond" => 606_300],
            ["name" => "Kitio Elianne", "role" => BaseRoleProperties::ROLE_USER, "fond" => 70_523],
            ["name" => "Kenfack Anne Alice", "role" => BaseRoleProperties::ROLE_USER, "fond" => 55_800],
            ["name" => "Kenfack Anne Alice", "role" => BaseRoleProperties::ROLE_USER, "fond" => 41_600],
            ["name" => "Djuikoo Vidal", "role" => BaseRoleProperties::ROLE_USER, "fond" => -47_600],
            ["name" => "Zebaze Annette", "role" => BaseRoleProperties::ROLE_USER, "fond" => 145_161],
            ["name" => "Zebaze Nguimzap Roderick", "role" => BaseRoleProperties::ROLE_USER, "fond" => 86_600],
            ["name" => "Donfack Kemtsa Marie Pascaline", "role" => BaseRoleProperties::ROLE_USER, "fond" => 59_815],
            ["name" => "Nguekeng Kevine", "role" => BaseRoleProperties::ROLE_USER, "fond" => 900_130],
            ["name" => "Nguegang Kenfack Reine Aimée", "role" => BaseRoleProperties::ROLE_USER, "fond" => 28_300],
            ["name" => "Nguefack Marie", "role" => BaseRoleProperties::ROLE_USER, "fond" => 41_500],
            ["name" => "Nguepi Nguedia Astride", "role" => BaseRoleProperties::ROLE_USER, "fond" => 49_600],
            ["name" => "Nguimfack Kemwe Dorice", "role" => BaseRoleProperties::ROLE_USER, "fond" => 146_687],
            ["name" => "Mbogning Virginie", "role" => BaseRoleProperties::ROLE_USER, "fond" => 4_500],
            ["name" => "Feujio Sidonie", "role" => BaseRoleProperties::ROLE_USER, "fond" => 46_020],
            ["name" => "Tsague Marie Pascale", "role" => BaseRoleProperties::ROLE_USER, "fond" => 17_500],
            ["name" => "Mbogning Leontine", "role" => BaseRoleProperties::ROLE_USER, "fond" => 34_500],
            ["name" => "Kenfack Nguepi Albertine", "role" => BaseRoleProperties::ROLE_USER, "fond" => 39_500],
            ["name" => "Mbogning Jiofack Abib", "role" => BaseRoleProperties::ROLE_USER, "fond" => -17_500],
            ["name" => "Jiomezi Songa Brachère", "role" => BaseRoleProperties::ROLE_USER, "fond" => -47_500],
            ["name" => "Mezazem Ghislain", "role" => BaseRoleProperties::ROLE_USER, "fond" => 37_930],
            ["name" => "Mezatio Chancelier", "role" => BaseRoleProperties::ROLE_USER, "fond" => -64_150],
        ];

        $roles = $this->collection(
            $this->getRoleRepository()->findAll()
        );

        $users = [];
        foreach($data as $item){
            // dd($roles[$roles->search(fn($elt)=>$elt->getName() == $item["role"])]);
            $user = (new User())
            ->setUsername($item["name"])
            ->setRole(
                $roles[$roles->search(fn($elt)=>$elt->getName() == $item["role"])]
            );
            if($item["name"] === "Nguimfack Nguimzap Jean"){
                $admin = $user;
                $user->setPassword("mot2passe");
                $user->setPseudo("Superadmin");
            }
            $this->getManager()->persist($user);

            $users[] = $user;
        }
        $this->getManager()->flush();

        foreach($users as $key => $user){
            $account = $user->getAccount();
            $fundInitial = (new Fund())
            ->setWording("Solde initial au 01 janvier 2021")
            ->setCashBalances(0)
            ->setUser($user)
            ->setAccount($account)
            ->setAdmin($admin)
            ->setYear(2021)
            ->setPreviousBalances(0)
            ->setCashInFlows($data[$key]["fond"]);
            $this->getManager()->persist($fundInitial);
            $user->setAdmin($admin);
            $account->setCashInflows($data[$key]["fond"]);
        }

        $this->getManager()->flush();
    }
}
