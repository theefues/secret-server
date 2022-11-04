<?php

class SecretService
{
    /**
     * Returns a secret by hash.
     * @param string $hash Hash of secret.
     * @return array
     */
    public function getSecret($hash)
    {
        $secretModel = new SecretModel();

        $secretEntry = $secretModel->getSecret($hash);

        if (empty($secretEntry)) {
            return ['error' => true, 'statusCode' => "HTTP/1.1 404 Not Found", 'message' => 'Secret does not exist.'];
        }

        if ((!is_null($secretEntry['expiresAt']) && strtotime($secretEntry['expiresAt']) < time()) || $secretEntry['expiresAfterViews'] <= $secretEntry['currentViews']) {
            return ['error' => true, 'statusCode' => "HTTP/1.1 410 Page Expired", 'message' => 'This secret has expired.'];
        }

        $secretModel->updateSecretViewCount($hash, intval($secretEntry['currentViews']) + 1);

        return ['error' => false, 'statusCode' => "HTTP/1.1 200 OK", 'message' => 'Secret has been succesfully retrieved.', 'content' => $secretEntry];
    }

    /**
     * Add a new secret.
     * @param array $secret Secret properties
     * @return array
     */
    public function addSecret($secret)
    {
        $secret['hash'] = md5(uniqid());
        $validator = new SecretValidatorService($secret);
        $isValid = $validator->validate();

        if (!$isValid) {
            return ['error' => true, 'message' => $validator->getErrors()];
        }

        if(isset($secret['expiresAfter']) && round(intval($secret['expiresAfter'])) > 0) {
            $secret['expiresAt'] = date('Y-m-d H:i:s', round(intval($secret['expiresAfter']))*60 + time());
        }
        
        unset($secret['expiresAfter']);

        $secretModel = new SecretModel();
        $secretModel->addSecret($secret);

        return ['error' => false, 'message' => 'Secret has been successfully added.', 'content' => ['hash' => $secret['hash']]];
    }
}
