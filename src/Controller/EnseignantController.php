<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class EnseignantController extends AbstractController
{
    #[Route('/enseignant', name: 'app_enseignant')]
    public function index(): Response
    {
         $file = $this->getParameter('kernel.project_dir') . '/public/uploads/enseignants.xlsx';

    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    $rows = $sheet->toArray();

    $enseignants = [];

    foreach ($rows as $index => $row) {

        if ($index === 0) {
            continue; // ignorer l'entête
        }

        $enseignants[] = [
            'nom' => $row[0],
            'diplome' => $row[1],
            'universite' => $row[2],
            'telephone' => $row[3],
            'email' => $row[4],
        ];
    }

        return $this->render('enseignant/index.html.twig', [
            'controller_name' => 'EnseignantController',
            'enseignants' => $enseignants
        ]);
    }
    #[Route('/admin-enseignant', name: 'app_enseignant_admin')]
    public function add(Request $request):Response{
        
    if ($request->isMethod('POST')) {

        $file = $request->files->get('fichier');

        if ($file) {

            $filename = 'enseignants.xlsx';

            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $filename
            );
        }

        return $this->redirectToRoute('app_enseignant');
    }

    return $this->render('enseignant/upload.html.twig');
    }
}
