<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\CommonCRUD;
use App\Traits\Filter;
use App\Traits\DbConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use Filter, CommonCRUD, DbConnection;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $this->setNewConnection();

        $config = [
            'filterKeys'=> [
                'id_user',
                'name',
                'lastname',
                'FullName',
                'username',
                'password',
                'MelliCode',
                'StudentCode',
                'student_tell',
                'BirthDay',
                'Reshte',
                'Paie',
                'Class',
                'Address',
                'studentMail',
                'gender',
                'picture',
                'fatherName',
                'fatherTell',
                'fatherJob',
                'fatherMelliCode',
                'fatherPassword',
                'fatherMail',
                'motherName',
                'motherLastName',
                'motherTell',
                'motherJob',
                'motherMelliCode',
                'motherPassword',
                'motherMail',
                'preRegistration',
                'deletedStatus',
                'user_type',
                'more_info',
                'manager_id',
                'background_type',
                'xp'
            ]
        ];

        return $this->commonIndex($request, User::class, $config);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
