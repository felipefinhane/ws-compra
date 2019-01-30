<?php

use Illuminate\Http\Request;
use App\User;
use App\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = $request->user();
    $user->token = $user->createToken($user->email)->accessToken;
    return $user;
});

Route::post('/users', function (Request $request) {
    $data =  $request->all();
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->token = $user->createToken($user->email)->accessToken;
        return $user;
    }
});

Route::middleware('auth:api')->put('/users', function (Request $request) {
    $user = $request->user();
    $data =  $request->all();
    if (isset($data['password']) && !empty($data['password'])) {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'string|min:6|confirmed'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $data['password'] = bcrypt($data['password']);
        }
    } elseif (isset($data['password']) && $data['password'] == '') {
        unset($data['password']);
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ]
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
    } else {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }
    $user->update($data);
    $user->token = $user->createToken($user->email)->accessToken;
    return $user;
});

Route::post('/login', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'password' => 'required|string',
    ]);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Authentication passed...
            $user = Auth()->user();
            $user->token = $user->createToken($user->email)->accessToken;
            return $user;
        } else {
            return false;
        }
    }
});

Route::middleware('auth:api')->get('/users', function (Request $request) {
    return User::all();
});

Route::get('/courses', function (Request $request) {
    $courses = Course::with('lessons')->get();
    return $courses;
});

// Route::get('/admin/create/course', function (Request $request) {
//     //CRIANDO CURSO
//     // $course = Course::create(
//     //     [
//     //         'title' => 'Curso de JS',
//     //         'author' => 'Felipe F.',
//     //         'description' => 'Curso de Javascript',
//     //         'image' => 'http://netcoders.com.br/wp-content/uploads/2015/10/js3.png',
//     //         'price' => 19.90,
//     //         'price_text' => '19,90'
//     //     ]
//     // );
//     // return $course;
// });

// Route::get('/admin/create/lesson', function (Request $request) {
//     $lesson = Course::find(1)->lessons()->create(
//         [
//             "ordem" =>  3,
//             "titulo" => "Duvidas",
//             "tempo" => "01:34",
//             "video" =>  "https://www.youtube.com/embed/9XWhNHvGhHU"
//         ]
//     );

//     // "ordem" =>  2,
//     // "titulo" => "Realizando a Instalação",
//     // "tempo" => "05:34",
//     // "video" =>  "https://www.youtube.com/embed/9XWhNHvGhHU"
//     //CRIANDO CURSO
//     // $course = Course::create(
//     //     [
//     //         'title' => 'Curso de JS',
//     //         'author' => 'Felipe F.',
//     //         'description' => 'Curso de Javascript',
//     //         'image' => 'http://netcoders.com.br/wp-content/uploads/2015/10/js3.png',
//     //         'price' => 19.90,
//     //         'price_text' => '19,90'
//     //     ]
//     // );
//     return $lesson;
// });
