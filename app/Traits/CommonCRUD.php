<?php

namespace App\Traits;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait CommonCRUD
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $modelClass
     * @param array $config
     * @return array|Closure|ResponseFactory|Response
     */
    public function commonIndex(Request $request, $modelClass, array $config = [])
    {
        // load variables
        $modelQuery = $modelClass::query();
        $configArray = $this->getConfigArray($config);
        $perPage = ($request->has('length')) ? $request->get('length') : 10;

        $this->buildFilterQuery($request,$modelQuery, $modelClass, $configArray);

        // load appends
        $attachedCollection = null;
        $setAppends = $configArray['setAppends'];
        if ($configArray['returnModelQuery']) {
            return $this->getModelQueryWithAttachedCollectionClosure($modelQuery, $perPage, $setAppends);
        } elseif (count($configArray['setAppends']) > 0) {
            $attachedCollection = $this->getAttachedCollection($modelQuery, $setAppends, $perPage);
//                $modelQuery->paginate($perPage)
//                    ->getCollection()->map(function (& $item) use ($setAppends) {
//                    return $item->setAppends($setAppends);
//                });
        }

        // return json response
        if(isset($attachedCollection)) {
            return $this->jsonResponseOk($modelQuery->paginate($perPage)->setCollection($attachedCollection));
        }
        return $this->jsonResponseOk($modelQuery->paginate($perPage));
    }

    private function buildFilterQuery($request, & $modelQuery, $modelClass, $configArray) {
        $this->sorting($request,$modelQuery);
        $this->select($configArray['select'],$modelQuery, $modelClass);
        $this->loadScopes($request, $modelQuery, $configArray['scopes']);
        $this->filterByDate($request, $modelQuery, $configArray['filterDate']);
        $this->filterByKeys($request, $modelQuery, $configArray['filterKeys']);
        $this->filterByRelationKeys($request, $modelQuery, $configArray['filterRelationKeys']);
        $this->filterByRelationIds($request, $modelQuery, $configArray['filterRelationIds']);
        $modelQuery->with($configArray['eagerLoads']);
    }

    private function getConfigArray($config) {
        $configArray = [
            'select' => $this->getDefault($config, 'select', []),
            'scopes' => $this->getDefault($config, 'scopes', []),
            'eagerLoads' => $this->getDefault($config, 'eagerLoads', []),
            'filterDate' => $this->getDefault($config, 'filterDate', []),
            'filterKeys' => $this->getDefault($config, 'filterKeys', []),
            'setAppends' => $this->getDefault($config, 'setAppends', []),
            'returnModelQuery' => $this->getDefault($config, 'returnModelQuery', []),
            'filterRelationIds' => $this->getDefault($config, 'filterRelationIds', []),
            'filterRelationKeys' => $this->getDefault($config, 'filterRelationKeys', [])
        ];

        return $configArray;
    }

    private function getAttachedCollection(& $updatedModelQuery, $setAppends, $perPage) {
        return $updatedModelQuery->paginate($perPage)
            ->getCollection()->map(function (& $item) use ($setAppends) {
                return $item->setAppends($setAppends);
            });
    }

    private function getModelQueryWithAttachedCollectionClosure($modelQuery, $perPage, $setAppends) {
        $responseWithAttachedCollection = function($updatedModelQuery) use($perPage, $setAppends) {
            $attachedCollection = $this->getAttachedCollection($updatedModelQuery, $setAppends, $perPage);
            return $this->jsonResponseOk(
                $updatedModelQuery->paginate($perPage)
                ->setCollection($attachedCollection)
            );
        };
        return [
            'responseWithAttachedCollection' => $responseWithAttachedCollection,
            'modelQuery' => $modelQuery
        ];
    }

    private function loadScopes(Request $request, & $modelQuery, $scopes) {
        foreach ($scopes as $item) {
            $scopeItem = ($request->has($item)) ? $request->get($item) : false;
            if ($scopeItem) {
                $modelQuery->$item();
            }
        }
    }

    private function filterByKeys(Request $request, & $modelQuery, $filterKeys) {
        foreach ($filterKeys as $item) {
            $this->filterByKey($request, $item, $modelQuery);
        }
    }

    private function filterByRelationKeys(Request $request, & $modelQuery, $filterRelationKeys) {
        foreach ($filterRelationKeys as $item) {
            $this->filterByRelationKey($request, $item, $modelQuery);
        }
    }

    private function filterByRelationIds(Request $request, & $modelQuery, $filterRelationIds) {
        foreach ($filterRelationIds as $item) {
            $this->filterByRelationId($request, $item, $modelQuery);
        }
    }

    private function select(array $select, & $modelQuery, $modelClass) {
        $tableName = (new $modelClass())->getTable();
        foreach ($select as $item) {
            if (!strpos($item, '.')) {
                $item = $tableName.'.'.$item;
            }
            $modelQuery->addSelect($item);
        }
    }

    private function sorting(Request $request, & $modelQuery) {
        $sortation_field = $request->get('sortation_field');
        $sortation_order = $request->get('sortation_order');

        if (!isset($sortation_field) || !isset($sortation_order)) {
            return;
        }

        if (!strpos($sortation_field, '.')) {
            $modelQuery->orderBy($sortation_field, strtoupper($sortation_order));
        } else {
            $modelQuery->orderByPowerJoins($sortation_field, strtoupper($sortation_order));
        }
    }

    private function getDefault(array $config = [], $key, $default) {
        return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $modelClass
     * @return Response
     */
    public function commonStore(Request $request, $modelClass)
    {
        $createdModel = $modelClass::create($request->all());
        return $this->show($createdModel->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $model
     * @return Response
     */
    public function commonUpdate(Request $request, $model)
    {
        $model->fill($request->all());

        if ($model->save()) {
            return $this->show($model->id);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'commonUpdate' => [
                        'مشکلی در ویرایش اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $model
     * @return Response
     */
    public function commonDestroy($model)
    {
        if ($model->delete()) {
            return $this->jsonResponseOk([ 'message'=> 'حذف با موفقیت انجام شد.' ]);
        } else {
            return $this->jsonResponseServerError([
                'errors' => [
                    'commonDestroy' => [
                        'مشکلی در حذف اطلاعات رخ داده است.'
                    ]
                ]
            ]);
        }
    }

    public function getHasRelations($modelClass, $relations) {
        $hasRelations = [];
        foreach ($relations as $relation) {
            if (!$modelClass->$relation()->exists()) {
                $hasRelations []= $relation;
            }
        }

        return $hasRelations;
    }
}
