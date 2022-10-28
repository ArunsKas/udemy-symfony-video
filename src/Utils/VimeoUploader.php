<?php

namespace App\Utils;

use App\Utils\Interfaces\UploaderInterface;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Security\Core\Security;

class VimeoUploader implements UploaderInterface
{

    public function __construct(Security $security)
    {
        $this->vimeoToken = $security->getUser()->getVimeoApiKey();
    }

    public function upload($file)
    {
        // TODO: Implement upload() method.
    }

    public function delete($path)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vimeo.com/videos/$path",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer {$this->vimeoToken}",
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache",
                "Accept: */*"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: ' . $err);
        } else {
            return true;
        }
    }
}
