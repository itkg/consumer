<?php

namespace Itkg\Monitoring;

/**
 * Classe FilePermissionTest
 *
 * Test les droits sur un fichier ou répertoire
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class FilePermissionTest extends Test
{
    /**
     * Le chemin à tester
     * @var string
     */
    protected $path;

    /**
     * Les permissions nécessaires
     * @var array
     */
    protected $permissions;

    /**
     * Constructeur
     * @param string $identifier Identifiant du test
     * @param string $path Chemin du dossier / fichier
     * @param string $permissions Tableau de permissions
     */
    public function __construct($identifier, $path = '', $permissions = array())
    {
        $this->path = $path;
        $this->permissions = $permissions;
        parent::__construct($identifier);
    }

    /**
     * Test les droits du chemin courant
     *
     * @return boolean
     * @throws \Exception
     */
    public function execute()
    {
        if (file_exists($this->path)) {
            $filePermissions = substr(sprintf('%o', fileperms($this->path)), -3);
            if (!in_array($filePermissions, $this->permissions)) {
                throw new \Exception(
                    sprintf(
                        'Le chemin : "%s" n\a pas les droits requis (Droits actuels : %s)',
                        $this->path,
                        $filePermissions
                    )
                );
            }
            return true;
        }

        throw new \Exception(
            sprintf('Le chemin : "%s" n\existe pas', $this->path)
        );
    }

    /**
     * Getter path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Setter path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Getter permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Setter permissions
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }
}
