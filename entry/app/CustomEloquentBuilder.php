<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Closure;

class CustomEloquentBuilder extends Builder
{
    /** @var CustomModel */
    protected $model;

    public function setUp(): void
    {
        $this->setUpFilters();
        $this->setUpOrders();
        $this->setUpLimitAndOffset();
    }

    public function setUpFilters(): void
    {
        if (is_array($this->query->wheres)) {
            $this->model->filters = [];
            foreach ($this->query->wheres as $where) {
                $this->model->filters[] = $this->parseWhereToFilter($where);
            }
        }
    }

    public function setUpOrders(): void
    {
        if (is_array($this->query->orders)) {
            $this->model->orders = [];
            foreach ($this->query->orders as $order) {
                $this->model->orders[] = $order;
            }
        }
    }

    public function setUpLimitAndOffset(): void
    {
        if (!is_null($this->query->offset)) {
            $this->model->offset = $this->query->offset;
        }
        if (!is_null($this->query->limit)) {
            $this->model->limit = $this->query->limit;
        }
    }


    public function parseWhereToFilter(array $where)
    {
        if ($where['type'] === 'Basic') {
            return [
                'filter' => $where['column'],
                'operator' => $where['operator'],
                'value' => $where['value']
            ];
        }

        if ($where['type'] === 'Nested' && count($where['query']->wheres) === 1) {
            return [
                'filter' => $where['query']->wheres[0]['column'],
                'operator' => $where['query']->wheres[0]['operator'],
                'value' => $where['query']->wheres[0]['value']
            ];
        }

        throw new \Exception("Unown type");
    }

    public function get($columns = ['*'])
    {
        $this->setUp();

        $data =  $this->model->retrieve();

        return $this->model->hydrate($data);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = func_num_args() === 5 ? value(func_get_arg(4)) : $this->getCountForPagination();

        $perPage = ($perPage instanceof Closure
            ? $perPage($total)
            : $perPage
        ) ?: $this->model->getPerPage();

        $results = $total
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    public function getCountForPagination($columns = ['*'])
    {
        $this->setUp();

        return $this->count($columns);
    }

    public function count($columns = '*')
    {
        $this->setUp();

        return $this->model->retrieveCount();
    }

    public function delete()
    {
        throw new \Exception("Not supported exception");
    }
}
