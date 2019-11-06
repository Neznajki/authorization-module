<?php declare(strict_types=1);


namespace App\Service;


class RegistryService
{

    /** @var string */
    protected $serverGuiUri;

    /**
     * RegistryService constructor.
     * @param string $serverGuiUri
     */
    public function __construct(string $serverGuiUri)
    {
        $this->serverGuiUri = $serverGuiUri;
    }

    /**
     * @return string
     */
    public function getServerGuiUri(): string
    {
        return $this->serverGuiUri;
    }
}
