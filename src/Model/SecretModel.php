<?php

/**
 * SecretModel Class
 */
class SecretModel extends DatabaseModel
{
    /**
     * Returns a secret by hash.
     * @param string $hash Hash of secret
     * @return array
     */
    public function getSecret($hash)
    {
        return $this->query("SELECT hash, expiresAt, createdAt, expiresAfterViews, currentViews, secret FROM secrets WHERE hash = ?", [$hash])->first();
    }

    /**
     * Add a new secret;
     * @param array $secret Secret entry
     * @return int
     */
    public function addSecret($secret)
    {
        
        if(isset($secret['expiresAfter']) && round(intval($secret['expiresAfter'])) > 0) {
            $secret['expiresAt'] = date('Y-m-d H:i:s', round(intval($secret['expiresAfter']))*60 + time());
        }
        
        unset($secret['expiresAfter']);
        
        return $this->insert('secrets', $secret);
    }

    /**
     * Update an existing secret view count.
     * @param string $hash Hash of secret to update
     * @param int $count New current views count
     * @return DatabaseMoel|int
     */
    public function updateSecretViewCount($hash, $count)
    {
        return $this->query('UPDATE secrets SET currentViews = ? WHERE hash = ?', [$count, $hash]);
    }
}
