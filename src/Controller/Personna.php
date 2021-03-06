<?php
declare(strict_types=1);
/******************************************************************************/
/**                                 Personna                                 **/
/** Auteur: viduc@mail.fr                                                    **/
/** github: https://github.com/viduc/personna                                **/
/** Licence: Apache 2.0                                                      **/
/******************************************************************************/

namespace Viduc\Personna\Controller;

use Viduc\Personna\Exceptions\PersonnaRepositoryException;
use Viduc\Personna\Exceptions\PersonnaRequetesException;
use Viduc\Personna\File\File;
use Viduc\Personna\Interfaces\Controller\UseCaseInterface;
use Viduc\Personna\Interfaces\File\FileInterface;
use Viduc\Personna\Interfaces\Presenters\PresenterInterface;
use Viduc\Personna\Interfaces\Reponses\ReponseInterface;
use Viduc\Personna\Interfaces\Requetes\RequeteInterface;
use Viduc\Personna\Model\ErreurModel;
use Viduc\Personna\Reponses\Reponse;
use Viduc\Personna\Reponses\ReponsePersonna;
use Viduc\Personna\Repository\PersonnaRepository;

class Personna implements UseCaseInterface
{
    /**
     * @var PersonnaRepository
     */
    private PersonnaRepository $repository;

    private RequeteInterface $requete;

    private ReponseInterface $reponse;

    public function __construct(string $path)
    {
        $this->repository = new PersonnaRepository(new File($path));
    }

    /**
     * @param RequeteInterface $requete
     * @param PresenterInterface $presenter
     * @return PresenterInterface
     */
    public function execute(
        RequeteInterface $requete,
        PresenterInterface $presenter
    ): PresenterInterface {
        $this->requete = $requete;
        match($requete->getAction()) {
            'create', 'update' => $this->reponsePersonna(
                $requete->getAction(),
                'personna'
            ),
            'read' => $this->reponsePersonna('read', 'id'),
            'delete' => $this->reponseVoid('delete', 'personna'),
            'getAll' => $this->reponsePersonna('getAll', '')
        };
        $presenter->presente($this->reponse);

        return $presenter;
    }

    /**
     * @param string $action
     * @param string $param
     * @return void
     */
    private function reponsePersonna(string $action, string $param): void
    {
        $this->reponse = new ReponsePersonna();
        try {
            $personnas = $param !== '' ?
                $this->repository->$action($this->requete->getParam($param)) :
                $this->repository->$action();
            $personnas = !is_array($personnas) ? [$personnas] : $personnas;
            $this->reponse->setPersonnas($personnas);
        } catch (PersonnaRepositoryException | PersonnaRequetesException $ex) {
            $this->reponse->setErreur(
                new ErreurModel($ex->getCode(), $ex->getMessage())
            );
        }
    }

    /**
     * @param string $action
     * @param string $param
     * @return void
     */
    private function reponseVoid(string $action, string $param): void
    {
        $this->reponse = new Reponse();
        try {
            $this->repository->delete($this->requete->getParam('personna'));
        } catch (PersonnaRepositoryException | PersonnaRequetesException $ex) {
            $this->reponse->setErreur(
                new ErreurModel($ex->getCode(), $ex->getMessage())
            );
        }
    }

    /**
     * @codeCoverageIgnore
     * @param FileInterface $file
     * @return void
     */
    final public function setFile(FileInterface $file): void
    {
        $this->repository->setFile($file);
    }

    /**
     * @codeCoverageIgnore
     * @param PersonnaRepository $repository
     * @return void
     */
    final public function setRepository(PersonnaRepository $repository): void
    {
        $this->repository = $repository;
    }
}