<?php

namespace App\Command;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Ingredient;
use Symfony\Component\Panther\Client;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\CssSelector\CssSelectorConverter;

#[AsCommand(
    name: 'app:import',
    description: 'Import des recettes de l\'ancien site',
)]
class ImportCommand extends Command
{
    private $ingredients = [];

    public function __construct(
        private KernelInterface $kernel,
        private HttpClientInterface $webClient,
        private EntityManagerInterface $em,
        private CategoryRepository $categoryRepository,
    ) {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
                // Définition des catégories de recettes à importer
        $categories = [
            [
                "name" => "Entrée",
                "url" => "https://luckydelices.fr/entrees.html",
            ],
            [
                "name" => "Plat",
                "url" => "https://luckydelices.fr/plats.html",
            ],
            [
                "name" => "Dessert",
                "url" => "https://luckydelices.fr/desserts.html"
            ]
        ];
        $io = new SymfonyStyle($input, $output);
        $converter = new CssSelectorConverter();
        // $url = "https://luckydelices.fr/ail-fermente.html";
        // $client = Client::createChromeClient();
        // Or, if you care about the open web and prefer to use Firefox
        $client = Client::createFirefoxClient();

        foreach ($categories as $cat) {
            // Récupération de la catégorie parente (Entrée, Plat, Dessert)
            $io->info(sprintf("Fetching %s recipe page", $cat['name']));

            $parent = $this->categoryRepository->findOneByName($cat['name']);
            // Si la catégorie parente n'existe pas, on la crée
            if (empty($parent)) {
                $parent = new Category();
                $parent->setName($cat['name']);
                $this->categoryRepository->save($parent, true);
            }

            // Initialisation de la barre de progression
            $progressIndicator = new ProgressIndicator($output);
            $progressIndicator->start('Fecthing in progress...');

            // Récupération de la page web de la catégorie
            $crawler = $client->request('GET', $cat['url']);
            // Extraction des sections de recettes depuis la page
            $sections = $crawler->filter('section')->each(function ($section) use ($progressIndicator) {
                $progressIndicator->advance();
                if ($section->children()->eq(0)->matches('h1')) {
                    return;
                }
                $categorytitle = $section->children("h2")->text();
                $recettes = $section->children('.card')->each(function ($card) {
                    return 'https://luckydelices.fr/' . $card->filter('.btn-primary')->getAttribute('href');
                });


                return [
                    "title" => $categorytitle,
                    "recettes" => $recettes,
                ];
            });

            $sections = array_filter($sections, function ($section) {
                return $section != null;
            });

            $progressIndicator->finish('Fecthing finished');

            foreach ($sections as $section) {
                // Importation des recettes de la section
                $io->info(sprintf("Importing %s recipes", $section['title']));

                $category = new Category;
                $category
                    ->setName($section['title'])
                    ->setParent($parent);

                $this->em->persist($category);
                $this->em->flush();

                $progressBar2 = new ProgressBar($output);
                // $iterable can be array
                foreach ($progressBar2->iterate($section["recettes"]) as $recette) {
                    $io->writeln(['', $recette, '']);

                    $this->importRecette($recette, $client, $io, $category);
                }
            }
        }


        // $crawler = $client->waitFor('Getting started');
        // $client->clickLink('Getting started');

        // // Wait for an element to be present in the DOM (even if hidden)
        // // Alternatively, wait for an element to be visible
        // $crawler = $client->waitForVisibility('#installing-the-framework');

        // // echo $crawler->filter('#installing-the-framework')->text();
        // $client->takeScreenshot('screen.png'); // Yeah, screenshot!
        // $io->success('Recettes importées avec succés');
        // $this->importRecette($url, $client, $io);
        return Command::SUCCESS;
    }

    public function importRecette($url, $client, $io, $category)
    {
        $crawler = $client->request('GET', $url); 
        try {
            $name = $crawler->filter('h1')->text();
        } catch (\Throwable $th) {
            die('H1 missing');
        }
        try {
            $sousTitre = $crawler->filter('h2')->text();
        } catch (\Throwable $th) {
            die('H2 missing');
        }
        try {
            $duree = $crawler->filter('p')->text();
            preg_match("/\d+/", $duree, $matches);
            $duree = $matches[0];
        } catch (\Throwable $th) {
            die('duree missing');
        }

        try {
            $level = $crawler->filter('.gauche')->children('p')->eq(1)->text();
        } catch (\Throwable $th) {
            die('level missing');
        }

        if (str_contains(strtolower($level), 'facile')) {
            $level = 'facile';
        }
        try {
            $person = $crawler->filter('.gauche')->children('p')->eq(2)->text();
        } catch (\Throwable $th) {
            die('person missing');
        }
        preg_match("/\d+/", $person, $matches);
        $person = $matches[0];
        try {
            $link = $crawler->filter('.video')->closest('a')->getAttribute('href');
        } catch (\Throwable $th) {
            die('link missing');
        }
        try {
            $ingredients = $crawler->filter('.ingredient')->children('li')->each(function ($ing) {
                return $ing->text();
            });
        } catch (\Throwable $th) {
            die('ingredients missing');
        }
        try {
            $instruction = $crawler->filter('.instruction')->text();
        } catch (\Throwable $th) {
            die('instruction missing');
        }
        try {
            $photo = $crawler->filter('.photo-plat')->children('img')->getAttribute('src');
        } catch (\Throwable $th) {
            dump('photo-plat missing');
        }

        try {
            if (empty($photo)) {
                $photo = $crawler->filter('.photo-plat2')->children('img')->getAttribute('src');
            }
        } catch (\Throwable $th) {
            dump('no picture found');
        }

        $photoName = explode("/", $photo)[1];
        $folder = $this->kernel->getProjectDir() . "/public/images/recipes/$photoName";
        $response = $this->webClient->request("GET", "https://luckydelices.fr/$photo");
        file_put_contents($folder, $response->getContent());

        // Création et enregistrement de l'objet Recipe dans la base de données
        $recette = new Recipe;
        $recette
            ->setName($name)
            ->setSubtitle($sousTitre)
            ->setTime($duree)
            ->setLevel($level)
            ->setPeople($person)
            ->setYoutube($link)
            ->setDescription($instruction)
            ->setCategory($category)
            ->setImage($photoName);


        // Importation des ingrédients de la recette
        foreach ($ingredients as $ingredient) {
            $filtered_ingredients = array_filter($this->ingredients, function (Ingredient $object) use ($ingredient) {
                return $object->getName() == $ingredient;
            });

            if (empty($filtered_ingredients)) {
                $ingre = new Ingredient();
                $ingre->setName($ingredient);
                $this->em->persist($ingre);
                $this->ingredients[] = $ingre;
                $recette
                    ->addIngredient($ingre);
            } else {
                $recette->addIngredient(array_values($filtered_ingredients)[0]);
            }
        }

        $this->em->persist($recette);
        $this->em->flush();
    }
}
