<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ModelShards\Board;
use App\ModelShards\Post;
use Illuminate\Support\Facades\DB;
use Utils\Hash;

class WelcomeController extends Controller
{
    public function index(){
        $model = Post::create([
                'data' => [
                    'name' => 'Ngo Dinh Canh',
                    'age' => '26',
                    'address' => 'Quang Nam Province'
                ]
                ],68719476737,false);
        dd($model);
            // $model = Post::find(6);
            // dd($model->data);
        dd(config("database.connections"));
    }
    public function update($id){
        $model = Post::update($id,[
            'data' => [
                'name' => 'Ngo Dinh Canh',
                'age' => '26',
                'address' => 'Quang Nam Province'
            ]
            ]);
        dd($model);
    }
    public function delete($id){
        $model = Post::delete($id);
        dd($model);
    }
    public function getInfo($id){
        dd(Hash::decode($id,true));
    }
}
