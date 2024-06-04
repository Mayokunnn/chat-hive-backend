<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Factory;

class AuthController extends Controller
{

    use Response;
    protected $storage;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env("FIREBASE_SERVICE_ACCOUNT")))
            ->withProjectId(env('FIREBASE_PROJECT_ID'))
            ->createStorage();

        $this->storage = $firebase;
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:8',
            ]);


            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('email', $request->input('email'))->first();

            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                throw new ModelNotFoundException();
            }

            // Start a session and store the user's ID in it
            $request->session()->put('user_id', $user->id);

            return $this->success('Logged in successfully', ['user' => $user]);
        } catch (ValidationException $e) {
            return $this->error('Registration Failed', $e->errors(), 422);
        } catch (ModelNotFoundException $e) {
            return $this->error('User not found', statusCode: 422);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'nullable|string|max:255|unique:users',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->username = $request->input('username');
            $user->password = Hash::make($request->input('password'));

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $localPath = $image->getPathname();

                $bucket = $this->storage->getBucket();
                $bucket->upload(fopen($localPath, 'r'), [
                    'name' => $fileName
                ]);

                $imageReference = $bucket->object($fileName);
                $imageUrl = $imageReference->signedUrl(new \DateTime('9999-12-31'));

                $user->image = $imageUrl;
            }


            $user->save();


            // Start a session and store the user's ID in it
            $request->session()->start();
            $request->session()->put('user_id', $user->id);
            $request->session()->save();

            return $this->success('You have been registered successfully', [new UserResource($user)]);
        } catch (ValidationException $e) {
            return $this->error('Registration Failed', $e->errors(), 422);
        }
    }
    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        return $this->success('You have logged out successfully');
    }

    //
}
