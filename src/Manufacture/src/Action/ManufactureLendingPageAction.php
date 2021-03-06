<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2017, Alpha-Hydro
 *
 */

namespace Manufacture\Action;

use Api\Entity\ManufactureCategories;
use Api\Entity\Pages;
use Doctrine\ORM\EntityManager;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;

class ManufactureLendingPageAction implements ServerMiddlewareInterface
{

    /**
     * @var Template\TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ManufactureLendingPageAction constructor.
     * @param EntityManager $entityManager
     * @param Template\TemplateRendererInterface $templateRenderer
     */
    public function __construct(EntityManager $entityManager, Template\TemplateRendererInterface $templateRenderer)
    {
        $this->entityManager = $entityManager;
        $this->templateRenderer = $templateRenderer;
    }


    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface|HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /** @var Pages $indexPage */
        $indexPage = $this->entityManager->getRepository(Pages::class)->findOneBy([
            'active' => 1,
            'deleted' => 0,
            'path' => 'manufacture',
        ]);

        if (!$indexPage)
            return new HtmlResponse($this->templateRenderer
                ->render('error::404'), 404);

        $categories = $this->entityManager->getRepository(ManufactureCategories::class)->findBy(
            [
                'parentId' => 0,
                'active' => 1,
                'deleted' => 0,
            ],
            ['sorting' => 'ASC']
        );

        $data = [
            'page' => $indexPage,
            'sidebarListItem' => $categories,
        ];

        return new HtmlResponse($this->templateRenderer->render('manufacture::lendingManufacture', $data));
    }
}