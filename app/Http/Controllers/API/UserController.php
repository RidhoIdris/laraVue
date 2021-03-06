<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index()
    {
        return User::latest()->paginate('10');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|max:255|min:8',
            'bio' => 'max:255',
            'type' => 'required|string|max:255'
        ]);

        return User::create([
            'name' => request('name'),
            'email' => request('email'),
            'bio' => request('bio'),
            'type' => request('type'),
            'photo' => request('photo'),
            'password' => Hash::make(request('password'))
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth('api')->user();
    }
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|string|max:255|min:8',
            'bio' => 'max:255',
            'type' => 'required|string|max:255'
        ]);

        $currentPhoto = $user->photo;
        if ($request->photo != $currentPhoto) {
           $name = time().'.'.explode('/',explode(':',substr($request->photo,0,strpos($request->photo,';')))[1])[1];
           \Image::make($request->photo)->save(public_path('images/profile/').$name);

           $request->merge(['photo' => $name]);
           $userPhoto = public_path('images/profile/').$currentPhoto;
            if (file_exists($userPhoto)) {
                @unlink($userPhoto);
            }
        }
        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        $user->update($request->all());
        return ['message' => 'Update Profile Success'];

    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|string|max:255|min:8',
            'bio' => 'max:255',
            'type' => 'required|string|max:255'
        ]);
        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request->password)]);
        }
        $user->update($request->all());

        return ['message'=> 'user Updateted'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id)->delete();

        return ['message' => 'User Deleted'];
    }
}
