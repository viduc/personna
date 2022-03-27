<?php
declare(strict_types=1);
/******************************************************************************/
/**                                 Personna                                 **/
/** Auteur: viduc@mail.fr                                                    **/
/** github: https://github.com/viduc/personna                                **/
/** Licence: Apache 2.0                                                      **/
/******************************************************************************/

namespace Viduc\Personna\tests\Repository;

use PHPUnit\Framework\TestCase;
use Viduc\Personna\Model\PersonnaModel;
use Viduc\Personna\Repository\PersonnaRepository;

class PersonnaRepositoryTest extends TestCase
{
    private PersonnaRepository $repository;

    final public function setUp(): void
    {
        parent::setUp();
        $this->repository = new PersonnaRepository();
    }

    /**
     * @test
     * @return void
     */
    final public function create(): void
    {
        self::assertInstanceOf(PersonnaModel::class, $this->repository->create([]));
    }

    /**
     * @test
     * @return void
     */
    final public function read(): void
    {
        self::assertInstanceOf(PersonnaModel::class, $this->repository->read(0));
    }

    /**
     * @test
     * @return void
     */
    final public function update(): void
    {
        $persona = new PersonnaModel();
        self::assertInstanceOf(PersonnaModel::class, $this->repository->update($persona));
    }

    /**
     * @test
     * @return void
     */
    final public function delete(): void
    {
        self::assertNull($this->repository->delete(new PersonnaModel()));
    }
}