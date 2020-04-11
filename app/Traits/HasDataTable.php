<?php

namespace DBSystem\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasDataTable
{
    protected function getDataTable(Builder $query, Request $request, array $columns = ['*'])
    {
        if ($request->query('searchField') && $request->query('searchValue')) {
            $term = '%' . $request->query('searchValue') . '%';
            if (is_array($request->query('searchField'))) {
                $i = 0;
                foreach ($request->query('searchField') as $field) {
                    $method = $i ? 'orWhere' : 'where';
                    $query = $query->{$method}($field, 'like',  $term);
                    $i++;
                }
            } else {
                $query = $query->where($request->query('searchField'), 'like',  $term);
            }
        }

        $orderBy = $request->query('sortField') ?? 'updated_at';
        $orderDir = $request->query('sortOrder') ?? 'desc';
        $query = $query->orderBy($orderBy, $orderDir);

        return $query->select($columns)
            ->paginate($request->query('perPage') ?? 10);
    }
}