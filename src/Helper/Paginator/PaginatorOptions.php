<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 19.03.17
 * Time: 12:40
 */

namespace App\Helper\Paginator;

use UnexpectedValueException;
use function in_array;

/**
 * Class PaginatorOptions
 */
class PaginatorOptions
{
    /**
     * @var int
     */
    private int $page = 0;

    /**
     * @var int
     */
    private int $maxPages = 0;

    /**
     * @var int
     */
    private int $limit = 10;

    /**
     * @var string
     */
    private string $order = 'id';

    /**
     * @var string
     */
    private string $orderDir = 'ASC';

    /**
     * @var array<string>
     */
    private array $criteria = [];

    /**
     * @param int $page
     *
     * @return PaginatorOptions
     */
    public function setPage(int $page): PaginatorOptions
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param string $order
     *
     * @return PaginatorOptions
     */
    public function setOrder(string $order): PaginatorOptions
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $orderDir
     *
     * @return PaginatorOptions
     */
    public function setOrderDir(string $orderDir): PaginatorOptions
    {
        $dirs = ['ASC', 'DESC'];
        $dir = strtoupper($orderDir);

        if (false === in_array($dir, $dirs, true)) {
            throw new UnexpectedValueException(
                sprintf('Order dir must be %s', implode(', ', $dirs))
            );
        }

        $this->orderDir = $orderDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDir(): string
    {
        return $this->orderDir;
    }

    /**
     * @param array<string> $criteria
     *
     * @return PaginatorOptions
     */
    public function setCriteria(array $criteria): PaginatorOptions
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @param int $maxPages
     *
     * @return PaginatorOptions
     */
    public function setMaxPages(int $maxPages): PaginatorOptions
    {
        $this->maxPages = $maxPages;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPages(): int
    {
        return $this->maxPages;
    }

    /**
     * @param int $limit
     *
     * @return PaginatorOptions
     */
    public function setLimit(int $limit): PaginatorOptions
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param string $name
     *
     * @return string Criteria value or empty string
     */
    public function searchCriteria(string $name): string
    {
        return array_key_exists($name, $this->criteria) ? $this->criteria[$name]
            : '';
    }
}
