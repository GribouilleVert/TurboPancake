<?php
namespace Framework\Twig;

use Framework\Router;
use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;

class PagerFantaExtension extends AbstractExtension
{

    /**
     * @var Router
     */
    private $router;

    /**
     * @var array
     */
    private $queryArgs;

    /**
     * @var string
     */
    private $route;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig\TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']]),
        ];
    }

    public function paginate(Pagerfanta $pagerFanta, string $route, array $queryArgs): string
    {
        $this->queryArgs = $queryArgs;
        $this->route = $route;

        if ($pagerFanta->hasPreviousPage()) {
            $prevLink = $this->makeUri($pagerFanta->getPreviousPage());
        } else {
            $prevClass = ['disabled'];
        }

        if ($pagerFanta->hasNextPage()) {
            $nextLink = $this->makeUri($pagerFanta->getNextPage());
        } else {
            $nextClass = ['disabled'];
        }

        $pagesLi = '';
        $offset = 0;
        $currentPage = $pagerFanta->getCurrentPage();
        $pageNb = $pagerFanta->getNbPages();

        if ($currentPage - 5 <= 1) {
            $offset = 2 - ($currentPage - 1);
            if ($offset < 0) {
                $offset = 0;
            }
        } elseif ($currentPage + 5 >= $pageNb) {
            $offset = -2 + ($pageNb - $currentPage);
            if ($offset > 0) {
                $offset = 0;
            }
        }

        if ($offset <= 0 AND $currentPage - 2 > 1) {
            $pagesLi .= $this->paginationLIWithLink(
                1,
                [],
                $this->makeUri(1)
            );
            if (!($currentPage - 3 <= 1)) {
                $pagesLi .= $this->paginationLI(
                    '<span>...</span>',
                    [],
                );
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            $liClass = [];
            if ($currentPage === $i + $offset - 3 + $currentPage) {
                $liClass[] = 'active';
            } else {
                $liLink = $this->makeUri($i + $offset - 3 + $currentPage);
            }
            $pagesLi .= $this->paginationLIWithLink(
                $i + $offset - 3 + $currentPage,
                $liClass??null,
                $liLink??null
            );
        }

        if ($offset >= 0 AND $currentPage + 2 < $pageNb) {
            if ($currentPage + 3 < $pageNb) {
                $pagesLi .= $this->paginationLI(
                    '<span>...</span>',
                    [],
                );
            }
            $pagesLi .= $this->paginationLIWithLink(
                $pageNb,
                [],
                $this->makeUri($pageNb)
            );
        }
        $result =  '<ul class="pagination">';
        $result .= $this->paginationLIWithLink(
            '<i class="icon icon-arrow-left"></i>',
            $prevClass??[],
            $prevLink??null
        );
        $result .= $pagesLi;
        $result .= $this->paginationLIWithLink(
            '<i class="icon icon-arrow-right"></i>',
            $nextClass??[],
            $nextLink??null
        );
        $result .= '</ul>';

        return $result;
    }

    private function makeUri(int $page): string
    {
        $queryArgs = $this->queryArgs;
        if ($page > 1) {
            $queryArgs['page'] = $page;
        }

        return $this->router->generateUri($this->route, [], $queryArgs);
    }

    private function paginationLIWithLink(string $content, array $classes = [], ?string $href = null): string
    {
        if (is_null($href)) {
            $href = '#';
        }
        $liContent = '<a href="' . $href . '">';
        $liContent .= $content;
        $liContent .= '</a></li>';

        return $this->paginationLI($liContent, $classes);
    }

    private function paginationLI(string $content, array $classes = []): string
    {
        $classesString = '';
        foreach ($classes as $class) {
            $classesString .= ' ' . $class;
        }

        $li =  '<li class="page-item' . $classesString . '">';
        $li .= $content;
        $li .= '</li>';

        return $li;
    }

}
