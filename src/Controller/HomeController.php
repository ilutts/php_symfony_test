<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class HomeController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('home/index.html.twig', [
                'controller_name' => $this->getShortNameClass(),
                'method_name' => __FUNCTION__,
                'now_date_time' => $this->getNowDateTime(),
                'trailers' => $this->fetchDataAll(),
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    public function trailer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $id = (int) $request->getAttribute('id');

        try {
            $data = $this->twig->render('home/trailer.html.twig', ['trailer' => $this->fetchDataById($id)]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    public function test(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $this->twig->render('hello.html.twig', ['name' => 'Creative']);

        $response->getBody()->write($data);

        return $response;
    }

    protected function fetchDataAll(): Collection
    {
        $data = $this->em->getRepository(Movie::class)
            ->findAll();

        return new ArrayCollection($data);
    }

    protected function fetchDataById(int $id): object
    {

        $data = $this->em->getRepository(Movie::class)
            ->find($id);

        return $data;
    }

    private function getShortNameClass()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    private function getNowDateTime()
    {
        return date('Y-m-d H:i');
    }
}
