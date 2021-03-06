<?php
declare(strict_types=1);
/******************************************************************************/
/**                                 Personna                                 **/
/** Auteur: viduc@mail.fr                                                    **/
/** github: https://github.com/viduc/personna                                **/
/** Licence: Apache 2.0                                                      **/
/******************************************************************************/
namespace Viduc\Personna\File;

use Viduc\Personna\Exceptions\PersonnaFileException;
use Viduc\Personna\Interfaces\File\FileInterface;
use Viduc\Personna\Interfaces\File\FolderInterface;
use Viduc\Personna\Model\PersonnaModel;

class File implements FileInterface
{
    private string $path;
    private PersonnaModel $personna;

    public function __construct(string $path, FolderInterface $folder = null)
    {
        $this->path = $path;
        $this->personna = new PersonnaModel();
        $folder = $folder ?? new Folder();
        $folder->create($path);
    }

    /**
     * @param PersonnaModel $personna
     * @return void
     * @throws PersonnaFileException
     */
    final public function create(PersonnaModel $personna): void
    {
        $file = $this->path. $personna->getUsername() . '.personna';
        if (!file_exists($file)) {
            try {
                file_put_contents(
                    $file,
                    json_encode($personna->jsonSerialize(), JSON_THROW_ON_ERROR)
                );// @codeCoverageIgnoreStart
            } catch (\JsonException $e) {
                throw new PersonnaFileException(
                    "L'enregistrement du personna " . $personna->getUsername() . " a échoué",
                    103
                );
            }// @codeCoverageIgnoreStop
        } else {
            throw new PersonnaFileException(
                'Le personna ' . $personna->getUsername() . ' existe  déjà',
                100
            );
        }
    }

    /**
     * @param PersonnaModel $personna
     * @return void
     * @throws PersonnaFileException
     */
    final public function update(PersonnaModel $personna): void
    {
        $file = $this->path. $personna->getUsername() . '.personna';
        if (!file_exists($file)) {
            throw new PersonnaFileException(
                "Le personna " . $personna->getUsername() . " n'existe pas",
                102
            );
        }
        $this->delete($personna);
        $this->create($personna);
    }

    /**
     * @param PersonnaModel $personna
     * @return void
     * @throws PersonnaFileException
     */
    final public function delete(PersonnaModel $personna): void
    {
        $file = $this->path. $personna->getUsername() . '.personna';
        if (!file_exists($file)) {
            throw new PersonnaFileException(
                "Le personna " . $personna->getUsername() . " n'existe pas",
                102
            );
        }
        unlink($file);
    }

    /**
     * @return PersonnaModel[]
     * @throws PersonnaFileException
     */
    final public function getAll(): array
    {
        $personnas = [];
        foreach(scandir($this->path) as $file) {
            if (str_contains($file, '.personna')) {
                $personnas[] = $this->chargerPersonna($file);
            }
        }

        return $personnas;
    }

    /**
     * @param string $file
     * @throws PersonnaFileException
     */
    private function chargerPersonna(string $file): PersonnaModel
    {
        try {
            $json = json_decode(
                file_get_contents($this->path . DIRECTORY_SEPARATOR . $file),
                false,
                512,
                JSON_THROW_ON_ERROR
            );
            $personna = new PersonnaModel();
            $personna->chargerDepuisJson($json);
            return $personna;
        // @codeCoverageIgnoreStart
        } catch (\JsonException $ex) {
            throw new PersonnaFileException(
                "Le chargement d'un fichier personna " . $file . " a échoué",
                101
            );
        }// @codeCoverageIgnoreStop
    }
}