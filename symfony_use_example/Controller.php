<?php

// use App\Service\Converter;

/**
 * A couple simple examples of use for this converter
 */
class SomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template("AcmeBundle:someDirectory:index.html.twig")
     */

    public function indexAction()
    {
        $cv = new Converter('men-sizes.txt');
        $data = $cv->setHeader(['US', 'EURO', 'UK', 'INCHES', 'CM'])->convert()->getData();
        $header = $cv->getHeader();

        return
            [
                'data'   => $data,
                'header' => $header
            ];
    }

    /**
     *
     * @Route("/someRoute", methods={"GET","POST"})
     * @Template("AcmeBundle:someDirectory:some.html.twig")
     */
    public function someAction(Request $request)
    {
        if ($this->getMethod() === 'POST') {

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('file');

            if (!empty($uploadedFile)) {
                $destination = 'some/path';
                $ext = $uploadedFile->guessExtension();
                $name = 'someName'.$ext;
                $uploadedFile->move($destination, $name);

                $cv = new Converter($destination .'/'. $name);
                $cv->setPath('some/new/path')->createFile('someSeparator: comma, space etc..');
            }
        }
        return [];
    }
}
