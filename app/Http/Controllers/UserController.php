<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = user::all();
        return view('users.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request -> validate([
            'name' => 'required|min:3',
            'email' => 'required',
            'role' => 'required'

        ]);

        
        $email = $request->email; // Anda harus mengganti ini dengan cara sesuai dengan form Anda
        $username = $request->name; // Anda harus mengganti ini dengan cara sesuai dengan form Anda

        $email_first_3_letters = substr($email, 0, 3);
        $username_first_3_letters = substr($username, 0, 3);
        $password = $email_first_3_letters . $username_first_3_letters;




        user::create([

            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $password,
        ]);

        return redirect()->back()->with('success', 'Berhasil menambahkan data user!');
    }


    /**
     * Display the specified resource.
     */
    public function show(user $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = user::find($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => '',

        ]);


        $email = $request->email; // Anda harus mengganti ini dengan cara sesuai dengan form Anda
        $username = $request->name; // Anda harus mengganti ini dengan cara sesuai dengan form Anda

        $email_first_3_letters = substr($email, 0, 3);
        $username_first_3_letters = substr($username, 0, 3);
        $password = $email_first_3_letters . $username_first_3_letters;


        user::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $password,

        ]);

        return redirect()->route('user.home')->with('success', 'Berhasil mengubah data!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        user::where('id', $id)->delete();
        return redirect()->back()->with('deleted', 'Berhasil menghapus data!');
    }

    public function loginAuth(Request $request){
        $request->validate([
            'email' => 'required|email:dns', // cek email valid atau tidak
            'password' => 'required|alpha_dash'//valid: _ a-z  A-Z, tidak ada spasi
        ],[
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password harus di isi',
            'password.alpha_dash' => 'Password harus di isi huruf dan karakter tanpa spasi'
        ]);

        $user = $request->only(['email', 'password']);

        if (Auth::attempt($user)) {
            return redirect()->route('home.page');
        }else{
            return redirect()->back()->with('failed', 'Proses login gagal, silahkan coba kembali dengan data yang benar');
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('logout', 'Anda telah logout');
    }
}