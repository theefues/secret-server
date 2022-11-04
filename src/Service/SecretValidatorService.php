<?php

/**
 * SecretValidatorService Class
 */
class SecretValidatorService
{
    /**
     * Secret to validate
     * @var array
     */
    private $_secret;

    /**
     * Occured errors
     * @var array
     */
    private $_errors;

    public function __construct($secret)
    {
        $this->_errors = [];
        $this->_secret = $secret;
    }

    /**
     * Validate secret entity.
     * @return void
     */
    private function _validate()
    {
        if (!isset($this->_secret)) {
            $this->_errors[] = 'Secret not set.';
        }

        if (!isset($this->_secret['expiresAfterViews']) || intval($this->_secret['expiresAfterViews']) <= 0) {
            $this->_errors[] = 'expiresAfterViews property must be set, must be a number and must be greater than 0.';
        }

        if (!isset($this->_secret['secret'])) {
            $this->_errors[] = 'secret property must be set!';
        }

        if (isset($this->_secret['expiresAfter']) && (is_numeric($this->_secret['expiresAfter']) === false || intval($this->_secret['expiresAfter']) < 0)) {
            $this->_errors[] = 'expiresAfter property must be a number and must be 0 or greater.';
        }

        if (!isset($this->_secret['hash'])) {
            $this->_errors[] = 'hash property must be set!';
        }
    }

    /**
     * Public validation function.
     * @return bool
     */
    public function validate()
    {
        $this->_validate();

        return empty($this->_errors) ? true : false;
    }

    /**
     * Return occured errors.
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
